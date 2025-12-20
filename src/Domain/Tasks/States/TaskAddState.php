<?php

namespace Domain\Tasks\States;

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

        Keyboard::send(
            "Раздел: Задачи\n" .
            "Добавить задачу\n" .
            "Введите название задачи в формате \"Помыть посуду 21.12.2025 17:00\"\n" .
            "Можно вводить сразу несколько задач, каждая на новой строке",
            $keyboard
        );
    }

    public function handle(): ?BotState
    {
        if (bot()->message()->getText() === KeyboardContract::BACK) {
            $newState = new MenuBotState(troute('tasks'));
            UserState::changeState(bot()->userId(), $newState);
            return $newState;
        }

        $tasks = bot()->message()->getText();
        $arTasks = explode("\n", $tasks);

        $response = [];

        foreach ($arTasks as $task) {
            $result = TaskRepository::save(bot()->userId(), $task);

            if ($result === TaskRepository::EXISTS) {
                $response[] = "Задача \"$task\" уже существует";
            }

            if ($result === TaskRepository::RESTORED) {
                $response[] = "Задача \"$task\" востановленна";
            }

            if ($result === TaskRepository::SUCCESS_SAVED) {
                $response[] = "Задача \"$task\" создана";
            }
        }

        if (!empty($response)) {
            bot()->sendMessage(implode("\n", $response));
        }

        return new TaskListState();
    }
}
