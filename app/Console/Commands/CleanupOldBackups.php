<?php

namespace App\Console\Commands;

use App\Http\Controllers\BackupController;
use Illuminate\Console\Command;

class CleanupOldBackups extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'backups:cleanup';

    /**
     * The console command description.
     */
    protected $description = 'Remove backup files and records older than 18 months';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Cleaning up old backups (older than 18 months)...');

        $count = BackupController::cleanupOldBackups();

        $this->info("Done. {$count} old backup(s) removed.");

        return Command::SUCCESS;
    }
}
