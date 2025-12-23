<?php

namespace Domain\Calendar\States;

use Domain\Calendar\Models\Timer;
use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Contracts\KeyboardContract;
use Domain\TelegramBot\MenuBotState;
use Illuminate\Support\Carbon;

class CalendarListState extends BotState
{
    public function render(): void
    {
        $userDto = tuser();

        $list = '';
        $num = 1;

        $timers = Timer::query()
            ->where('telegram_user_id', $userDto->userId)
            ->get();

        foreach ($timers as $timer) {
            $time = Carbon::make($timer->startDate)
                ->setTimezone(tusertimezone());
            $list .= "$num) $timer->title: $time\n";
            $num++;
        }

        if (empty($list)) {
            $list = 'Пусто...';
        }

        send("Раздел: Календарь\nСписок событий:\n$list", keyboard()->back());
    }

    public function handle(): ?BotState
    {
        if (bot()->message()->getText() === KeyboardContract::BACK) {
            $newState = new MenuBotState(troute('calendar'));
            tuserstate()->changeState($newState);
            return $newState;
        }

        return new CalendarListState();
    }
}
