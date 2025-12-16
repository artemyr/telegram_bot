<?php

namespace Domain\Calendar\Actions;

use App\Jobs\WorkSession;
use Domain\TelegramBot\Facades\UserState;

class WorkAction
{
    public const NAME = 'work_session';

    public function __invoke(): void
    {
        $userDto = UserState::load(bot()->userId());

        if (!empty($userDto->actions[self::NAME]) && $userDto->actions[self::NAME] === true) {
            bot()->sendMessage("Вы уже запустили таймер!");
            return;
        }

        $pause = config('calendar.actions.work.pause_duration');
        bot()->sendMessage("Через $pause минут отдых. Я напомню");

        dispatch(new WorkSession(
            bot()->chatId(),
            bot()->userId(),
            'Пора отдыхать',
            self::NAME,
            self::NAME . '_' . bot()->userId()
        ))->delay(now()->addMinutes($pause));

        UserState::changeAction(bot()->userId(), self::NAME, true);
    }
}
