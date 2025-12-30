<?php

namespace Domain\Tasks\Repository;

use App\Jobs\Tasks\Recurrence\GenerateOneTaskOccurrencesJob;
use Domain\Tasks\Contracts\RecurrenceTaskRepositoryContract;
use Domain\Tasks\Models\Task;
use Domain\Tasks\Models\TaskRecurrence;
use Domain\TelegramBot\Models\TelegramUser;
use Illuminate\Support\Collection;
use Support\Dto\RepositoryResult;

class RecurrenceTaskRepository implements RecurrenceTaskRepositoryContract
{
    public function save(int $userId, string $title, string $date): RepositoryResult
    {
        $tuser = TelegramUser::query()
            ->select(['id', 'telegram_id', 'timezone'])
            ->where('telegram_id', $userId)
            ->first();

        $task = Task::firstOrCreate([
            'telegram_user_id' => $tuser->telegram_id,
            'title' => $title,
            'repeat' => true,
        ]);

        $parsedDate = humandateparser($date, $tuser->timezone);

        if ($parsedDate->isError()) {
            return RepositoryResult::error('Не удалось определить график повторений');
        }

        $recurrences = $parsedDate->getCollection();

        foreach ($recurrences as $recurrence) {
            TaskRecurrence::firstOrCreate([
                'task_id' => $task->id,
                'type' => $parsedDate->getType(),
                'days_of_week' => $recurrence->daysOfWeek ?? null,
                'days_of_month' => $recurrence->daysOfMonth ?? null,
                'time' => $recurrence->time,
                'is_active' => true,
                'start_date' => null,
                'end_date' => null,
            ]);
        }

        dispatch(new GenerateOneTaskOccurrencesJob($task->id));

        return new RepositoryResult(RepositoryResult::SUCCESS_SAVED);
    }

    public function findByUserId(int $userId): Collection
    {
        return Task::query()
            ->select(['id', 'title', 'deadline'])
            ->with('taskRecurrences')
            ->sorted()
            ->repeat()
            ->where('telegram_user_id', $userId)
            ->get();
    }

    public function deleteById(int $userId, int $id): RepositoryResult
    {
        $task = Task::query()
            ->where('id', $id)
            ->repeat()
            ->first();

        if (empty($task)) {
            return new RepositoryResult(RepositoryResult::ERROR, null, 'Задача не найдена');
        }

        $task->delete();

        return new RepositoryResult(RepositoryResult::SUCCESS_DELETED, $task);
    }
}
