<?php

namespace App\Jobs;

use App\Models\BackupLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use ZipArchive;

class ProcessBackup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of times the job may be attempted.
     */
    public int $tries = 1;

    /**
     * Timeout in seconds (30 minutes).
     */
    public int $timeout = 1800;

    public function __construct(
        protected int $backupLogId
    ) {}

    public function handle(): void
    {
        $backup = BackupLog::findOrFail($this->backupLogId);

        // If it was cancelled/failed before it started, just abort
        if ($backup->status === 'failed') {
            return;
        }

        // Mark as running
        $backup->update([
            'status'     => 'running',
            'started_at' => now(),
        ]);

        try {
            $backupDir = sys_get_temp_dir() . '/backups_' . uniqid();
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            $localFilePath = match ($backup->type) {
                'database' => $this->backupDatabase($backupDir, $backup),
                'files'    => $this->backupFiles($backupDir, $backup),
                'full'     => $this->backupFull($backupDir, $backup),
            };

            // Upload ZIP to S3
            $s3Path = 'backups/' . $backup->name;
            Storage::put($s3Path, file_get_contents($localFilePath));

            // Cleanup local temp files
            @unlink($localFilePath);
            $this->cleanupTempDir($backupDir);

            $fileSize = Storage::size($s3Path);

            $backup->update([
                'status'       => 'completed',
                'file_path'    => $s3Path,
                'file_size'    => $fileSize,
                'completed_at' => now(),
            ]);

            Log::info("Backup completed: {$backup->name}", [
                'id'   => $backup->id,
                'type' => $backup->type,
                'size' => $fileSize,
            ]);
        } catch (\Throwable $e) {
            $backup->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
                'completed_at'  => now(),
            ]);

            Log::error("Backup failed: {$backup->name}", [
                'id'    => $backup->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Backup database using mysqldump.
     */
    protected function backupDatabase(string $backupDir, BackupLog $backup): string
    {
        $sqlFile = $backupDir . '/' . pathinfo($backup->name, PATHINFO_FILENAME) . '.sql';
        $zipFile = $backupDir . '/' . $backup->name;

        $host     = config('database.connections.mysql.host');
        $port     = config('database.connections.mysql.port', '3306');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        $command = [
            'mysqldump',
            '--host=' . $host,
            '--port=' . $port,
            '--user=' . $username,
            '--password=' . $password,
            '--skip-ssl',
            '--no-tablespaces',
            '--single-transaction',
            '--routines',
            '--triggers',
            '--add-drop-table',
            $database,
        ];

        $process = new Process($command);
        $process->setTimeout(600); // 10 minutes

        $output = '';
        $process->run(function ($type, $buffer) use (&$output) {
            if ($type === Process::OUT) {
                $output .= $buffer;
            }
        });

        if (!$process->isSuccessful()) {
            throw new \RuntimeException('mysqldump failed: ' . $process->getErrorOutput());
        }

        // Write SQL to file
        file_put_contents($sqlFile, $output);

        // ZIP the SQL file
        $zip = new ZipArchive();
        if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Failed to create ZIP archive');
        }
        $zip->addFile($sqlFile, basename($sqlFile));
        $zip->close();

        // Remove raw SQL file
        @unlink($sqlFile);

        return $zipFile;
    }

    /**
     * Backup storage files from S3 (MinIO).
     * Downloads all files from the S3 bucket and packages them into a ZIP.
     */
    protected function backupFiles(string $backupDir, BackupLog $backup): string
    {
        $zipFile = $backupDir . '/' . $backup->name;

        $zip = new ZipArchive();
        if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Failed to create ZIP archive');
        }

        $this->addS3FilesToZip($zip, 'storage');
        $zip->close();

        return $zipFile;
    }

    /**
     * Backup both database and S3 files.
     */
    protected function backupFull(string $backupDir, BackupLog $backup): string
    {
        $zipFile = $backupDir . '/' . $backup->name;
        $sqlFile = $backupDir . '/temp_db_' . $backup->id . '.sql';

        // 1. Dump database
        $host     = config('database.connections.mysql.host');
        $port     = config('database.connections.mysql.port', '3306');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        $command = [
            'mysqldump',
            '--host=' . $host,
            '--port=' . $port,
            '--user=' . $username,
            '--password=' . $password,
            '--skip-ssl',
            '--no-tablespaces',
            '--single-transaction',
            '--routines',
            '--triggers',
            '--add-drop-table',
            $database,
        ];

        $process = new Process($command);
        $process->setTimeout(600);

        $output = '';
        $process->run(function ($type, $buffer) use (&$output) {
            if ($type === Process::OUT) {
                $output .= $buffer;
            }
        });

        if (!$process->isSuccessful()) {
            throw new \RuntimeException('mysqldump failed: ' . $process->getErrorOutput());
        }

        file_put_contents($sqlFile, $output);

        // 2. Create ZIP with both DB dump and S3 files
        $zip = new ZipArchive();
        if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Failed to create ZIP archive');
        }

        // Add SQL dump
        $zip->addFile($sqlFile, 'database/' . $database . '_dump.sql');

        // Add S3 storage files
        $this->addS3FilesToZip($zip, 'storage');

        $zip->close();

        // Clean up temp SQL file
        @unlink($sqlFile);

        return $zipFile;
    }

    /**
     * Download all files from S3 (MinIO) and add them to a ZIP archive.
     * Excludes the 'backups/' folder to avoid recursive backup.
     */
    protected function addS3FilesToZip(ZipArchive $zip, string $zipPrefix): void
    {
        $allFiles = Storage::allFiles();

        foreach ($allFiles as $file) {
            // Skip backup files to avoid recursive backup
            if (str_starts_with($file, 'backups/') || str_starts_with($file, 'backups\\')) {
                continue;
            }

            try {
                $contents = Storage::get($file);
                if ($contents !== null) {
                    $zip->addFromString($zipPrefix . '/' . $file, $contents);
                }
            } catch (\Throwable $e) {
                Log::warning("Backup: skipping file {$file}: {$e->getMessage()}");
            }
        }
    }

    /**
     * Recursively remove a temporary directory.
     */
    protected function cleanupTempDir(string $dir): void
    {
        if (!is_dir($dir)) return;

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $file) {
            if ($file->isDir()) {
                @rmdir($file->getPathname());
            } else {
                @unlink($file->getPathname());
            }
        }

        @rmdir($dir);
    }
}
