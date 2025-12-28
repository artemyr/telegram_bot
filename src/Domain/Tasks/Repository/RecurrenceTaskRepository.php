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

        $task = Task::firstOrCreate([
            'telegram_user_id' => $tuser->telegram_id,
            'title' => $title
        ]);

        $parsedDate = humandateparser($date);

        if ($parsedDate->isError()) {
            return RepositoryResult::error('Не удалось определить график повторений');
        }
//
//        $rtask = TaskRecurrence::firstOrCreate([
//            'task_id' => $task->id,
//            'type' => $parsedDate->getType(),
//            'rule' => $parsedDate->getRule(),
//            'start_at' => null,
//            'end_at' => null,
//        ]);

        return new RepositoryResult(RepositoryResult::SUCCESS_SAVED);
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
        return new RepositoryResult(RepositoryResult::SUCCESS_DELETED);
    }
}
