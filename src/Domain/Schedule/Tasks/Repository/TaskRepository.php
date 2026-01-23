<?php

namespace Domain\Schedule\Tasks\Repository;

use Domain\Schedule\Tasks\Contracts\TaskRepositoryContract;
use Domain\Schedule\Tasks\Models\Task;
use Domain\TelegramBot\Models\TelegramUser;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Support\Dto\RepositoryResult;

class TaskRepository implements TaskRepositoryContract
{
    public function save(int $userId, string $task): RepositoryResult
    {
        $title = $task;

        $tuser = TelegramUser::query()
            ->where('telegram_id', $userId)
            ->first();

        if (preg_match("~\d{2}\.\d{2}\.\d{4} \d{2}:\d{2}~", $task, $matches)) {
            $deadline = Carbon::make($matches[0], $tuser->timezone)
                ->setTimezone(config('app.timezone'));
            $title = str_replace($matches[0], '', $task);
        }

        if (!empty($deadline) && $deadline->isPast()) {
            return RepositoryResult::error('Дедлайн задачи должен буть будущее');
        }

        $title = trim($title);

        $task = Task::query()
            ->where('telegram_user_id', $userId)
            ->where('title', $title)
            ->withTrashed()
            ->first();

        if (!empty($task) && !$task->trashed()) {
            return new RepositoryResult(RepositoryResult::EXISTS, $task);
        }

        if (!empty($task) && $task->trashed()) {
            $task->restore();
            return new RepositoryResult(RepositoryResult::RESTORED, $task);
        }

        if (empty($task)) {
            $task = Task::create([
                'telegram_user_id' => $userId,
                'title' => $title,
                'deadline' => $deadline ?? null,
            ]);

            return new RepositoryResult(RepositoryResult::SUCCESS_SAVED, $task);
        }

        return new RepositoryResult(RepositoryResult::ERROR);
    }

    public function findByUserId(int $userId): Collection
    {
        return Task::query()
            ->select(['id', 'title', 'deadline', 'created_at'])
            ->sorted()
            ->single()
            ->where('telegram_user_id', $userId)
            ->get();
    }

    public function deleteById(int $userId, int $id): RepositoryResult
    {
        $task = Task::query()
            ->where('id', $id)
            ->single()
            ->first();

        if (empty($task)) {
            return new RepositoryResult(RepositoryResult::ERROR, null, 'Задача не найдена');
        }

        $task->delete();

        return new RepositoryResult(RepositoryResult::SUCCESS_DELETED, $task);
    }
}
