<?php

namespace App\Providers;

use Domain\Schedule\Tasks\Contracts\RecurrenceTaskRepositoryContract;
use Domain\Schedule\Tasks\Contracts\TaskRepositoryContract;
use Domain\Schedule\Tasks\Repository\RecurrenceTaskRepository;
use Domain\Schedule\Tasks\Repository\TaskRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public $bindings = [
        TaskRepositoryContract::class => TaskRepository::class,
        RecurrenceTaskRepositoryContract::class => RecurrenceTaskRepository::class,
    ];
}
