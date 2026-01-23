<?php

namespace Domain\Tasks\States;

use Domain\Tasks\Contracts\RecurrenceTaskRepositoryContract;
use Domain\Tasks\Presentations\RecurrenceTaskPresentation;
use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Enum\KeyboardEnum;
use Domain\TelegramBot\Exceptions\PrintableException;
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
        $tasks = $this->taskRepository->findByUserId(bot()->userId());
        $table = (new RecurrenceTaskPresentation($tasks, tusertimezone()))->getTable();

        message()
            ->text([
                "Раздел: Повторяющиеся задачи",
                "Список задач:",
                "Чтобы удалить задачу, отправте ее номер",
                (string)$table
            ])
            ->inlineKeyboard(keyboard()->back())
            ->send();
    }

    /**
     * @throws PrintableException
     */
    public function handle(): void
    {
        if (bot()->isCallbackQuery()) {
            $query = bot()->callbackQuery()->data;

            if ($query === KeyboardEnum::BACK->value) {
                keyboard()->remove();
                $newState = new MenuBotState(troute('tasks'));
                tuser()->changeState($newState);
                return;
            }
        } else {
            $text = bot()->message()?->getText();

            if (filter_var($text, FILTER_VALIDATE_INT)) {
                $userDto = tuser()->get();

                $tasks = $this->taskRepository->findByUserId($userDto->userId);
                $table = (new RecurrenceTaskPresentation($tasks, tusertimezone()))->getTable();

                $row = $table->getRow((int)$text);

                if (empty($row)) {
                    throw new PrintableException('Выберите из списка');
                }

                $result = $this->taskRepository->deleteById($userDto->userId, $row->getCol('id')->value);

                if ($result->state === RepositoryResult::SUCCESS_DELETED) {
                    message("Задача \"{$result->model->title}\" удалена");
                }

                if ($result->state === RepositoryResult::ERROR) {
                    throw new PrintableException($result->message);
                }

                return;
            }
        }
    }
}
