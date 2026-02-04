<?php

namespace Domain\Travel\States\Find;

use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Enum\KeyboardEnum;
use Domain\TelegramBot\MenuBotState;
use Domain\Travel\Models\TravelClaim;
use Domain\Travel\Models\TravelFormat;

class HowState extends AbstractState
{
    protected static array $how = [];
    protected ?TravelClaim $claim;

    public function __construct(?string $path = null)
    {
        parent::__construct($path);

        $this->claim = TravelClaim::query()
            ->where('telegram_user_id', nutgram()->userId())
            ->first();

        TravelFormat::query()->get()->each(function (TravelFormat $travelFormat) {
            self::$how[] = $travelFormat->title;
        });
    }

    public function render(): void
    {
        $keyboard = self::$how;
        $keyboard[] = KeyboardEnum::BACK->label();

        message()
            ->text([
                "Выбор формата",
            ])
            ->replyKeyboard($keyboard)
            ->send();
    }

    public function handle(): BotState
    {
        if (empty($this->claim)) {
            message('Ваша заявка потеряна. Начните заного');
            return new WhereState();
        }

        $query = nutgram()->message()?->getText();

        if ($query === KeyboardEnum::BACK->label()) {
            return new MenuBotState(troute('home'));
        }

        if (!$this->validate($query, self::$how)) {
            message('Выберите из списка');
            return $this;
        }

        $format = TravelFormat::query()
            ->select('id')
            ->where('title', $query)
            ->first();

        if ($format) {
            $this->claim->travel_format_id = $format->id;
            $this->claim->save();
        }

        $claim = TravelClaim::query()
            ->select(['id', 'telegram_user_id','date_from','date_to'])
            ->where('telegram_user_id', nutgram()->userId())
            ->with(['travelFormat','travelResort'])
            ->first();

        message()
            ->text([
                "Где: {$claim->format->title}",
                "Когда: $claim->date_from $claim->date_to",
                "Как: {$claim->resort->title}",
            ])
            ->send();

        return new MenuBotState(troute('home'));
    }
}
