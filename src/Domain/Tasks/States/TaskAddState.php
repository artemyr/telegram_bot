<?php

namespace Domain\Tasks\States;

use Domain\Tasks\Contracts\TaskRepositoryContract;
use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Contracts\KeyboardContract;
use Domain\TelegramBot\Exceptions\PrintableException;
use Domain\TelegramBot\MenuBotState;
use Support\Dto\RepositoryResult;

class TaskAddState extends BotState
{
    protected TaskRepositoryContract $taskRepository;

    public function __construct(protected ?string $path = null)
    {
        $this->taskRepository = app(TaskRepositoryContract::class);
        parent::__construct($path);
    }

    public function render(): void
    {
        $response = [
            "Раздел: Задачи",
            "Добавить задачу",
            "Введите название задачи в формате \"Помыть посуду 21.12.2025 17:00\"",
            "Можно вводить сразу несколько задач, каждая на новой строке",
        ];

        send($response, keyboard()->back());
    }

    /**
     * @throws PrintableException
     */
    public function handle(): ?BotState
    {
        if (bot()->message()->getText() === KeyboardContract::BACK) {
            $newState = new MenuBotState(troute('tasks'));
            tuserstate()->changeState($newState);
            return $newState;
        }

        $tasks = bot()->message()->getText();
        $arTasks = explode("\n", $tasks);

        $response = [];

        foreach ($arTasks as $task) {
            $result = $this->taskRepository->save(bot()->userId(), $task);

            if ($result->state === RepositoryResult::EXISTS) {
                $response[] = "Задача \"$task\" уже существует";
            }

            if ($result->state === RepositoryResult::RESTORED) {
                $response[] = "Задача \"$task\" востановленна";
            }

            if ($result->state === RepositoryResult::SUCCESS_SAVED) {
                $response[] = "Задача \"$task\" создана";
            }

            if ($result->state === RepositoryResult::ERROR) {
                throw new PrintableException("Задача \"$task\" ошибка\n$result->message");
            }
        }

        if (!empty($response)) {
            send($response);
        }

        return new TaskListState();
    }
}
