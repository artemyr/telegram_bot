<?php

namespace Domain\Tasks\States;

use Domain\Tasks\Models\Task;
use Domain\Tasks\Repository\TaskRepository;
use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Contracts\KeyboardContract;
use Domain\TelegramBot\Dto\Table\ColDto;
use Domain\TelegramBot\Dto\Table\RowDto;
use Domain\TelegramBot\Dto\Table\TableDto;
use Domain\TelegramBot\Exceptions\PrintableException;
use Domain\TelegramBot\Facades\Keyboard;
use Domain\TelegramBot\Facades\UserState;
use Domain\TelegramBot\MenuBotState;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TaskListState extends BotState
{
    public bool $silent = true;

    public function render(): void
    {
        $userDto = tuser();

        $table = TaskRepository::getTable($userDto->userId);

        $list = (string) $table;

        if ($table->empty()) {
            $list = 'Пусто...';
        }

        Keyboard::back("Раздел: Задачи\n"
            . "Список задач:\n"
            . "Чтобы пометить задачу выполненной, отправте ее номер\n"
            . "$list");
    }

    /**
     * @throws PrintableException
     */
    public function handle(): ?BotState
    {
        $text = bot()->message()->getText();

        if ($text === KeyboardContract::BACK) {
            $newState = new MenuBotState(troute('tasks'));
            UserState::changeState(bot()->userId(), $newState);
            return $newState;
        }

        if (filter_var($text, FILTER_VALIDATE_INT)) {
            try {
                $userDto = tuser();

                $table = TaskRepository::getTable($userDto->userId);

                $row = $table->getRow((int)$text);

                if (empty($row)) {
                    throw new ModelNotFoundException();
                }

                $task = Task::query()
                    ->where('title', $row->getCol('title'))
                    ->firstOrFail();

            } catch (ModelNotFoundException) {
                throw new PrintableException('Выберите из списка');
            }

            $task->delete();
        }

        return new TaskListState();
    }
}
