<?php

namespace Domain\Calendar\Actions;

use App\Jobs\WorkSession;
use Domain\TelegramBot\Facades\UserState;

class StartWorkAction
{
    public const NAME = 'start_work';

    public function __invoke(): void
    {
        $userDto = UserState::load(bot()->userId());

        if (!empty($userDto->actions[self::NAME]) && $userDto->actions[self::NAME] === true) {
            bot()->sendMessage("Вы уже запустили рабочий день!");
            return;
        }

        $hour = config('calendar.actions.work.start_work');
        $end = now()->addHours($hour);

        bot()->sendMessage("Вы начали рабочий день. Напомню вам когда его нужно будет завершить. В $end");

        dispatch(new WorkSession(
            bot()->chatId(),
            bot()->userId(),
            'Пора завершать рабочий день!',
            self::NAME,
            self::NAME . '_' . bot()->userId()
        ))->delay($end);

        UserState::changeAction(bot()->userId(), self::NAME, true);
    }
}
