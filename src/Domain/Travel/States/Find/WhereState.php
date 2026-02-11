<?php

namespace Domain\Travel\States\Find;

use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Enum\KeyboardEnum;
use Domain\TelegramBot\MenuBotState;
use Domain\Travel\Models\TravelResort;
use Domain\Travel\States\AbstractState;
use Illuminate\Support\Collection;

class WhereState extends AbstractState
{
    public const STAGE_MAIN = 'stage_main';
    public const STAGE_OTHER = 'stage_other';

    protected static array $where = [];

    protected string $stage;

    public function __construct()
    {
        if (empty($this->stage)) {
            $this->stage = self::STAGE_MAIN;
        }
    }

    private function getWhere(): array
    {
        if (empty(self::$where)) {
            TravelResort::query()
                ->chunk(2, function (Collection $items) {
                    if ($items->count() > 1) {
                        self::$where[] = [
                            $items->first()->title,
                            $items->last()->title,
                        ];
                    } else {
                        self::$where[] = $items->first()->title;
                    }
                });
            self::$where[] = "Другое (?)";
        }

        return self::$where;
    }

    public function render(): void
    {
        if ($this->stage === self::STAGE_MAIN) {
            $keyboard = $this->getWhere();
            $keyboard[] = KeyboardEnum::BACK->label();

            message()
                ->text([
                    "Найти компанию",
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
            if (!$this->validate($query, $this->getWhere())) {
                message('Выберите из списка');
                return $this;
            }

            if ($query === 'Другое (?)') {
                $this->stage = self::STAGE_OTHER;
                return $this;
            }
        }

        $resort = TravelResort::query()
            ->select('id')
            ->where('title', $query)
            ->first();

        if ($resort) {
            $claim = $this->createClaim();
            $claim->travel_resort_id = $resort->id;
            $claim->save();

            return new WhenState();
        }

        return $this;
    }
}
