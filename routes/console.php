<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('queue:work --stop-when-empty')->everyMinute()->withoutOverlapping();
Schedule::command('backup:clean')->dailyAt('02:00');
Schedule::command('backup:run')->dailyAt('02:15');
Schedule::command('rankings:snapshot')->dailyAt('03:00')->withoutOverlapping();
