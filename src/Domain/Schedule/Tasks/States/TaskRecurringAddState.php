<?php

namespace Domain\Schedule\Tasks\States;

use Domain\Schedule\Tasks\Contracts\RecurrenceTaskRepositoryContract;
use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Enum\KeyboardEnum;
use Domain\TelegramBot\MenuBotState;
use RuntimeException;
use Support\Dto\RepositoryResult;

class TaskRecurringAddState extends BotState
{
    public const TITLE_STAGE = 'title';
    public const DATE_STAGE = 'date';
    protected RecurrenceTaskRepositoryContract $taskRepository;

    public function __construct(
        protected ?string $path = null,
        protected ?string $stage = null,
        protected ?string $title = null,
    )
    {
        $this->taskRepository = app(RecurrenceTaskRepositoryContract::class);

        if (empty($this->stage)) {
            $this->stage = self::TITLE_STAGE;
        }

        parent::__construct($path);
    }

    public function render(): void
    {
        $text = [];

        if ($this->stage === self::TITLE_STAGE) {
            $text[] = "Раздел: Задачи";
            $text[] = "Добавить повторяющуюся задачу";
            $text[] = "Введите название задачи";
        }

        if ($this->stage === self::DATE_STAGE) {
            $text[] = "Напишите когда эта здача должна повторяться";
            $text[] = "Например:";
            $text[] = "Понедельник в 10:00 Среда в 17:00";
            $text[] = "или";
            $text[] = "вторник в 7:00";
            $text[] = "или";
            $text[] = "1,5,10 числа в 14:00";
            $text[] = " или ";
            $text[] = "1 числа в 13:00";
            $text[] = "4 числа в 18:00";
            $text[] = "или";
            $text[] = "каждый день в 10:00";
        }

        message()
            ->text($text)
            ->inlineKeyboard(keyboard()->back())
            ->send();
    }

    public function handle(): BotState
    {
        if (nutgram()->isCallbackQuery()) {
            $query = nutgram()->callbackQuery()->data;

            if ($query === KeyboardEnum::BACK->value) {
                keyboard()->remove();
                return new MenuBotState(troute('schedule.tasks'));
            }
        } else {
            if ($this->stage === self::TITLE_STAGE) {
                $title = nutgram()->message()->getText();
                return new TaskRecurringAddState($this->path, self::DATE_STAGE, $title);
            }

            if ($this->stage === self::DATE_STAGE) {
                $repeat = nutgram()->message()->getText();

                $result = $this->taskRepository->save(nutgram()->userId(), $this->title, $repeat);

                if ($result->state === RepositoryResult::SUCCESS_SAVED) {
                    message("Задача \"$this->title\" создана");
                    return new MenuBotState(troute('schedule.tasks'));
                }

                if ($result->state === RepositoryResult::ERROR) {
                    message("Ошибка создания задачи \"$this->title\"");
                    return $this;
                }
            }

            throw new RuntimeException('Unknown stage');
        }

        return $this;
    }
}
