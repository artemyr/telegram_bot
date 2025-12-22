<?php

namespace App\Providers;

use Domain\Tasks\Contracts\RecurrenceTaskRepositoryContract;
use Domain\Tasks\Contracts\TaskRepositoryContract;
use Domain\Tasks\Repository\RecurrenceTaskRepository;
use Domain\Tasks\Repository\TaskRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public $bindings = [
        TaskRepositoryContract::class => TaskRepository::class,
        RecurrenceTaskRepositoryContract::class => RecurrenceTaskRepository::class,
    ];
}
