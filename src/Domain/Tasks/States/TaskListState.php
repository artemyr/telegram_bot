<?php

namespace Domain\Tasks\States;

use Domain\Tasks\Contracts\TaskRepositoryContract;
use Domain\Tasks\Presentations\TaskPresentation;
use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Contracts\KeyboardContract;
use Domain\TelegramBot\Exceptions\PrintableException;
use Domain\TelegramBot\MenuBotState;
use Support\Dto\RepositoryResult;

class TaskListState extends BotState
{
    protected TaskRepositoryContract $taskRepository;

    public function __construct(protected ?string $path = null)
    {
        $this->taskRepository = app(TaskRepositoryContract::class);
        parent::__construct($path);
    }

    public function render(): void
    {
        $userDto = tuser();

        $tasks = $this->taskRepository->findByUserId($userDto->userId);
        $table = (new TaskPresentation($tasks, tusertimezone()))->getTable();

        $response = [
            "Раздел: Задачи",
            "Список задач:",
            "Чтобы пометить задачу выполненной, отправте ее номер",
            (string)$table
        ];

        message()->text($response)->replyKeyboard(keyboard()->back())->send();
    }

    /**
     * @throws PrintableException
     */
    public function handle(): ?BotState
    {
        $text = bot()->message()->getText();

        if ($text === KeyboardContract::BACK) {
            $newState = new MenuBotState(troute('tasks'));
            tuserstate()->changeState($newState);
            return $newState;
        }

        if (filter_var($text, FILTER_VALIDATE_INT)) {
            $userDto = tuser();

            $tasks = $this->taskRepository->findByUserId($userDto->userId);
            $table = (new TaskPresentation($tasks))->getTable();

            $row = $table->getRow((int)$text);

            if (empty($row)) {
                throw new PrintableException('Выберите из списка');
            }

            $result = $this->taskRepository->deleteById($userDto->userId, $row->getCol('id')->value);

            if ($result->state === RepositoryResult::SUCCESS_DELETED) {
                message("Задача \"{$result->model->title}\" помечена выполненной");
            }

            if ($result->state === RepositoryResult::ERROR) {
                throw new PrintableException($result->message);

            }
        }

        return new TaskListState();
    }
}
