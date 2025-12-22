<?php

namespace Domain\Tasks\Contracts;

use Illuminate\Support\Collection;
use Support\Dto\RepositoryResult;

interface RecurrenceTaskRepositoryContract
{
    public function save(int $userId, string $title, string $date): RepositoryResult;
    public function findByUserId(int $userId): Collection;
    public function deleteById(int $userId, int $id): RepositoryResult;
}
