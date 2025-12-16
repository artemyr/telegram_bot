<?php

namespace Domain\Calendar\States;

use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Enum\MenuEnum;
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

        foreach ($userDto->actions as $action) {
            if ($action->finished === true) {
                continue;
            }
            $time = Carbon::make($action->startDate)
                ->setTimezone(config('app.timezone'));
            $list .= "$num) $action->title: $time\n";
            $num++;
        }

        if (empty($list)) {
            $list = 'Пусто...';
        }

        Keyboard::back("Раздел: Календарь\nСписок событий:\n$list");
    }

    public function handle(): ?BotState
    {
        if (bot()->message()->getText() === MenuEnum::BACK->value) {

            Keyboard::remove();

            request()->merge([
                'path' => troute('calendar')
            ]);

            return new MenuBotState();
        }

        return new CalendarListState();
    }
}
