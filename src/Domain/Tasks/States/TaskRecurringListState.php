<?php

namespace Domain\Tasks\States;

use Domain\Tasks\Contracts\RecurrenceTaskRepositoryContract;
use Domain\Tasks\Presentations\RecurrenceTaskPresentation;
use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Contracts\KeyboardContract;
use Domain\TelegramBot\Exceptions\PrintableException;
use Domain\TelegramBot\Facades\Keyboard;
use Domain\TelegramBot\Facades\UserState;
use Domain\TelegramBot\MenuBotState;
use Support\Dto\RepositoryResult;

class TaskRecurringListState extends BotState
{
    protected RecurrenceTaskRepositoryContract $taskRepository;

    public function __construct(protected ?string $path = null)
    {
        $this->taskRepository = app(RecurrenceTaskRepositoryContract::class);
        parent::__construct($path);
    }

    public function render(): void
    {
        $userDto = tuser();

        $tasks = $this->taskRepository->findByUserId($userDto->userId);
        $table = (new RecurrenceTaskPresentation($tasks, tusertimezone()))->getTable();

        $response = [
            "Раздел: Повторяющиеся задачи",
            "Список задач:",
            "Чтобы удалить задачу, отправте ее номер",
            (string)$table
        ];

        send($response, Keyboard::back());
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
            $userDto = tuser();

            $tasks = $this->taskRepository->findByUserId($userDto->userId);
            $table = (new RecurrenceTaskPresentation($tasks, tusertimezone()))->getTable();

            $row = $table->getRow((int)$text);

            if (empty($row)) {
                throw new PrintableException('Выберите из списка');
            }

            $result = $this->taskRepository->deleteById($userDto->userId, $row->getCol('id')->value);

            if ($result->state === RepositoryResult::SUCCESS_DELETED) {
                send("Задача \"{$result->model->task->title}\" удалена");
            }

            if ($result->state === RepositoryResult::ERROR) {
                throw new PrintableException($result->message);

            }
        }

        return new TaskRecurringListState();
    }
}
