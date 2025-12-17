<?php

namespace Domain\Tasks\States;

use Domain\Tasks\Models\Task;
use Domain\Tasks\Repository\TaskRepository;
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
        $result = TaskRepository::save(bot()->userId(), $taskTitle);

        if ($result === TaskRepository::EXISTS) {
            bot()->sendMessage("Задача \"$taskTitle\" уже существует");
            return new TaskAddState();
        }

        if ($result === TaskRepository::RESTORED) {
            bot()->sendMessage("Задача \"$taskTitle\" востановленна");
            return new TaskListState();
        }

        if ($result === TaskRepository::SUCCESS_SAVED) {
            bot()->sendMessage("Задача \"$taskTitle\" создана");
        }

        return new TaskListState();
    }
}
