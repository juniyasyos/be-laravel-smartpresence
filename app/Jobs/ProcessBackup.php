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

            // We only do database backup regardless of type, because files are on S3.
            $localFilePath = $this->backupDatabase($backupDir, $backup);

            $s3Path = 'backups/' . $backup->name;
            Storage::put($s3Path, file_get_contents($localFilePath));
            
            // Cleanup local temp files
            @unlink($localFilePath);
            @rmdir($backupDir);

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
}
