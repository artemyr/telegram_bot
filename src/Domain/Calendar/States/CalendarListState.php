<?php

namespace Domain\Calendar\States;

use Domain\Calendar\Models\Timer;
use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Contracts\KeyboardContract;
use Domain\TelegramBot\Facades\Keyboard;
use Domain\TelegramBot\Facades\UserState;
use Domain\TelegramBot\MenuBotState;
use Illuminate\Support\Carbon;

class CalendarListState extends BotState
{
    public bool $silent = true;

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
                ->setTimezone(config('app.timezone'));
            $list .= "$num) $timer->title: $time\n";
            $num++;
        }

        if (empty($list)) {
            $list = 'Пусто...';
        }

        Keyboard::back("Раздел: Календарь\nСписок событий:\n$list");
    }

    public function handle(): ?BotState
    {
        if (bot()->message()->getText() === KeyboardContract::BACK) {
            $newState = new MenuBotState(troute('calendar'));
            UserState::changeState(bot()->userId(), $newState);
            return $newState;
        }

        return new CalendarListState();
    }
}
