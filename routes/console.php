<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Clean up backups older than 18 months – runs daily at 2:00 AM
Schedule::command('backups:cleanup')->dailyAt('02:00');
