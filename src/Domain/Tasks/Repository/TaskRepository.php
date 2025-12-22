<?php

namespace Domain\Tasks\Repository;

use Domain\Tasks\Models\Task;
use Domain\TelegramBot\Dto\Table\ColDto;
use Domain\TelegramBot\Dto\Table\RowDto;
use Domain\TelegramBot\Dto\Table\TableDto;
use Domain\TelegramBot\Models\TelegramUser;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class TaskRepository
{
    public const SUCCESS_SAVED = 0;
    public const ERROR = 1;
    public const EXISTS = 2;
    public const RESTORED = 3;

    public static function save(int $userId, string $task): int
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

        $title = trim($title);

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
            Task::create([
                'telegram_user_id' => $userId,
                'title' => $title,
                'deadline' => $deadline ?? null,
            ]);

            return self::SUCCESS_SAVED;
        }

        return self::ERROR;
    }

    public static function getTable(int $userId): TableDto
    {
        $tasks = Task::query()
            ->select(['title', 'deadline'])
            ->sorted()
            ->where('telegram_user_id', $userId)
            ->get();

        return self::makeTable($tasks);
    }

    public static function makeTable(Collection $tasks, ?TelegramUser $user = null): TableDto
    {
        if (!empty($user)) {
            $timezone = $user->timezone;
        } else {
            $timezone = tusertimezone();
        }

        $table = new TableDto();
        foreach ($tasks as $task) {

            $now = now($timezone);
            $deadline = $task->deadline
                ?->setTimezone($timezone);

            $diff = $now->diffForHumans($deadline);

            $row = new RowDto();

            $row->addCol(new ColDto($task->title, 'title'));
            $row->addCol(new ColDto(
                $deadline
                    ?->format('d.m.Y H:i'),
                'deadline'
            ));

            if ($deadline) {
                $row->addCol(new ColDto("($diff)", 'diff'));
            }

            $table->addRow($row);
        }

        return $table;
    }
}
