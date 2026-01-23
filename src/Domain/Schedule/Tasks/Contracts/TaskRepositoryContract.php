<?php

namespace Domain\Schedule\Tasks\Contracts;

use Illuminate\Support\Collection;
use Support\Dto\RepositoryResult;

interface TaskRepositoryContract
{
    public function save(int $userId, string $task): RepositoryResult;
    public function findByUserId(int $userId): Collection;
    public function deleteById(int $userId, int $id): RepositoryResult;
}
