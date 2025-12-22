<?php

namespace Domain\Tasks\Repository;

use App\Models\TaskRecurrence;
use Domain\Tasks\Contracts\RecurrenceTaskRepositoryContract;
use Domain\Tasks\Models\Task;
use Domain\TelegramBot\Models\TelegramUser;
use Illuminate\Support\Collection;
use Support\Dto\RepositoryResult;

class RecurrenceTaskRepository implements RecurrenceTaskRepositoryContract
{
    public function save(int $userId, string $title, string $date): RepositoryResult
    {
        $tuser = TelegramUser::query()
            ->where('telegram_id', $userId)
            ->first();

        TaskRecurrence::firstOrCreate([
            'task_id' => '',
            'type' => '',
            'rule' => '',
            'start_at' => '',
            'end_at' => '',
        ]);
    }

    public function findByUserId(int $userId): Collection
    {
        return Task::query()
            ->select(['id', 'title', 'deadline'])
            ->with('taskRecurrence')
            ->sorted()
            ->repeat()
            ->where('telegram_user_id', $userId)
            ->get();
    }

    public function deleteById(int $userId, int $id): RepositoryResult
    {
        // TODO: Implement deleteById() method.
    }
}
