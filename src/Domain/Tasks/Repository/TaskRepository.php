<?php

namespace Domain\Tasks\Repository;

use Domain\Tasks\Models\Task;

class TaskRepository
{
    public const SUCCESS_SAVED = 0;
    public const ERROR = 1;
    public const EXISTS = 2;
    public const RESTORED = 3;

    public static function save(int $userId, string $title): int
    {
        $task = Task::query()
            ->where('telegram_user_id', $userId)
            ->where('title', $title)
            ->withTrashed()
            ->first();

        if (!empty($task) && !$task->trashed()) {
           return self::EXISTS;
        }

        if (!empty($task) && $task->trashed()) {
            $task->restore();
            return self::RESTORED;
        }

        if (empty($task)) {
            $task = new Task();
            $task->telegram_user_id = $userId;
            $task->title = $title;
            $task->save();
            return self::SUCCESS_SAVED;
        }

        return self::ERROR;
    }
}
