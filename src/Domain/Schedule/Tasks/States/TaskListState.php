<?php

namespace Domain\Schedule\Tasks\States;

use Domain\Schedule\Tasks\Contracts\TaskRepositoryContract;
use Domain\Schedule\Tasks\Presentations\TaskPresentation;
use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Enum\KeyboardEnum;
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
        $tasks = $this->taskRepository->findByUserId(nutgram()->userId());
        $table = (new TaskPresentation($tasks, tusertimezone()))->getTable();

        message()
            ->text([
                "Раздел: Задачи",
                "Список задач:",
                "Чтобы пометить задачу выполненной, отправте ее номер",
                (string)$table
            ])
            ->inlineKeyboard(keyboard()->back())
            ->send();
    }

    /**
     * @throws PrintableException
     */
    public function handle(): BotState
    {
        if (nutgram()->isCallbackQuery()) {
            $query = nutgram()->callbackQuery()->data;

            if ($query === KeyboardEnum::BACK->value) {
                keyboard()->remove();
                return new MenuBotState(troute('tasks'));
            }
        } else {
            $text = nutgram()->message()?->getText();

            if (filter_var($text, FILTER_VALIDATE_INT)) {
                $userDto = tuser()->get();

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
        }

        return $this;
    }
}
