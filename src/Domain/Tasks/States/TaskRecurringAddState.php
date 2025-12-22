<?php

namespace Domain\Tasks\States;

use Domain\Tasks\Contracts\RecurrenceTaskRepositoryContract;
use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Contracts\KeyboardContract;
use Domain\TelegramBot\Facades\Keyboard;
use Domain\TelegramBot\Facades\UserState;
use Domain\TelegramBot\MenuBotState;
use RuntimeException;

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
            $text[] = "Понедельник 10:00";
            $text[] = "Среда 17:00";
            $text[] = "или";
            $text[] = "1,5,10 числа месяца в 14:00";
        }

        send($text, Keyboard::back());
    }

    public function handle(): ?BotState
    {
        if (bot()->message()->getText() === KeyboardContract::BACK) {
            $newState = new MenuBotState(troute('tasks'));
            UserState::changeState(bot()->userId(), $newState);
            return $newState;
        }

        if ($this->stage === self::TITLE_STAGE) {
            $title = bot()->message()->getText();
            return new TaskRecurringAddState($this->path, self::DATE_STAGE, $title);
        }

        if ($this->stage === self::DATE_STAGE) {
            $repeat = bot()->message()->getText();

           $this->taskRepository->save(bot()->userId(), $this->title, $repeat);

            send("Задача \"$this->title\" создана");
            return new MenuBotState(troute('tasks'));
        }

        throw new RuntimeException('Unknown stage');
    }
}
