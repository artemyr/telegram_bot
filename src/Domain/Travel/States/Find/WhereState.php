<?php

namespace Domain\Travel\States\Find;

use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Enum\KeyboardEnum;
use Domain\TelegramBot\MenuBotState;
use Domain\Travel\Models\TravelResort;
use Illuminate\Support\Collection;

class WhereState extends AbstractState
{
    public const STAGE_MAIN = 'stage_main';
    public const STAGE_OTHER = 'stage_other';

    protected static array $where = [];

    protected string $stage;

    public function __construct(?string $path = null, ?string $stage = null)
    {
        parent::__construct($path);

        if (empty($stage)) {
            $this->stage = self::STAGE_MAIN;
        }

        TravelResort::query()
            ->chunk(2, function (Collection $items) {
                self::$where[] = [
                    $items->first()->title,
                    $items->last()->title,
                ];
            });
        self::$where[] = "Другое (?)";
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

        if ($this->stage === self::STAGE_OTHER) {
            $keyboard[] = KeyboardEnum::BACK->label();

            message()
                ->text([
                    "Введите название курорта",
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

        if ($this->stage === self::STAGE_MAIN) {
            if (!$this->validate($query, self::$where)) {
                message('Выберите из списка');
                return $this;
            }

            if ($query === 'Другое (?)') {
                $this->stage = self::STAGE_OTHER;
                return $this;
            }
        }

        return new WhenState();
    }
}
