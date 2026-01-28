<?php

namespace Domain\Travel\States\Find;

use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Enum\KeyboardEnum;
use Domain\TelegramBot\MenuBotState;

class WhereState extends AbstractState
{
    public const STAGE_MAIN = 'stage_main';
    public const STAGE_OTHER = 'stage_other';

    protected static array $where = [
        [
            "Роза хутор",
            "Красная поляна"
        ],
        [
            "Газпром",
            "Шерегеш"
        ],
        "Другое (?)",
    ];

    protected string $stage;

    public function __construct(?string $path = null, ?string $stage = null)
    {
        parent::__construct($path);

        if (empty($stage)) {
            $this->stage = self::STAGE_MAIN;
        }
    }

    public function render(): void
    {
        if ($this->stage === self::STAGE_MAIN) {
            message()->removeLast();

            $keyboard = self::$where;
            $keyboard[] = KeyboardEnum::BACK->label();

            message()
                ->text([
                    "Раздел: Найти компанию",
                    "Выбор курорта",
                ])
                ->replyKeyboard($keyboard)
                ->send();
        }
    }

    public function handle(): BotState
    {
        $query = nutgram()->message()?->getText();

        if ($query === KeyboardEnum::BACK->label()) {
            return new MenuBotState('home');
        }

        if (!$this->validate($query, self::$where)) {
            message('Выберите из списка');
            return $this;
        }

        if ($query === 'Другое (?)') {
            $this->stage = self::STAGE_OTHER;
            return $this;
        }

        return new WhenState();
    }
}
