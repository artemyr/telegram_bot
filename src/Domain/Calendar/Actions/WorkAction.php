<?php

namespace Domain\Calendar\Actions;

use App\Jobs\WorkSession;
use Domain\TelegramBot\Dto\ActionStateDto;
use Domain\TelegramBot\Facades\UserState;

class WorkAction
{
    public const TITLE = 'Таймер трудовой сессии';
    public const CODE = 'work_session';

    public function __invoke(): void
    {
        $userDto = UserState::load(bot()->userId());

        if (!empty($userDto->actions[self::CODE]) && $userDto->actions[self::CODE]->finished === false) {
            bot()->sendMessage("Вы уже запустили таймер!");
            return;
        }

        $pause = config('calendar.actions.work.pause_duration');
        bot()->sendMessage("Через $pause минут отдых. Я напомню");

        $startDate = now()->addMinutes($pause);

        $action = new ActionStateDto(
            self::class,
            false,
            now(),
            $startDate,
            self::CODE,
            self::TITLE,
        );

        dispatch(new WorkSession(
            bot()->chatId(),
            bot()->userId(),
            'Пора отдыхать',
            $action,
            self::CODE . '_' . bot()->userId()
        ))->delay($startDate);

        UserState::changeAction(bot()->userId(), $action);
    }
}
