<?php

use Domain\Schedule\Tasks\Jobs\MorningNotify\GlobalTaskRemindJob;
use Domain\Schedule\Tasks\Jobs\Recurrence\GenerateTaskOccurrencesJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new GlobalTaskRemindJob())
    ->daily()
    ->withoutOverlapping();

Schedule::job(new GenerateTaskOccurrencesJob())
    ->weekly()
    ->withoutOverlapping();

Schedule::call(function () {
    Artisan::call('model:prune', [
        '--model' => config('app.prunable_models', []),
    ]);
})->monthly();
