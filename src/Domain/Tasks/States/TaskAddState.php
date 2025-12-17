<?php

namespace Domain\Tasks\States;

use Domain\Tasks\Models\Task;
use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Contracts\KeyboardContract;
use Domain\TelegramBot\Facades\Keyboard;
use Domain\TelegramBot\Facades\UserState;
use Domain\TelegramBot\MenuBotState;

class TaskAddState extends BotState
{
    public bool $silent = true;

    public function render(): void
    {
        $keyboard = [
            KeyboardContract::BACK
        ];

        Keyboard::send("Раздел: Задачи\nДобавить задачу\nВведите название задачи", $keyboard);
    }

    public function handle(): ?BotState
    {
        if (bot()->message()->getText() === KeyboardContract::BACK) {
            UserState::changePath(bot()->userId(), troute('tasks'));

            return new MenuBotState();
        }

        $taskTitle = bot()->message()->getText();

        $task = Task::query()
            ->where('telegram_user_id', bot()->userId())
            ->where('title', $taskTitle)
            ->withTrashed()
            ->first();

        if (!empty($task) && !$task->trashed()) {
            bot()->sendMessage("Задача \"$task->title\" уже существует");
            return new TaskAddState();
        }

        if (!empty($task) && $task->trashed()) {
            $task->restore();
            bot()->sendMessage("Задача \"$task->title\" востанновлена");
            return new TaskListState();
        }

        if (empty($task)) {
            $task = new Task();
            $task->telegram_user_id = bot()->userId();
            $task->title = $taskTitle;
            $task->save();
            bot()->sendMessage("Задача \"$task->title\" создана");
        }

        return new TaskListState();
    }
}
