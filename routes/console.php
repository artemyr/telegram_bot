<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new \App\Jobs\Telegram\Schedule\Tasks\MorningNotify\GlobalTaskRemindJob())
    ->daily()
    ->withoutOverlapping();

Schedule::job(new \App\Jobs\Telegram\Schedule\Tasks\Recurrence\GenerateTaskOccurrencesJob())
    ->weekly()
    ->withoutOverlapping();

Schedule::call(function () {
    Artisan::call('model:prune', [
        '--model' => config('app.prunable_models', []),
    ]);
})->monthly();
