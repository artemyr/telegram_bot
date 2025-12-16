<?php

namespace Domain\Calendar\Actions;

use App\Jobs\TelegramActionJob;
use Domain\TelegramBot\Dto\ActionStateDto;
use Domain\TelegramBot\Facades\UserState;
use Illuminate\Support\Carbon;

class WorkSessionAction
{
    public const TITLE = 'Таймер трудовой сессии';
    public const CODE = 'work_session';

    public function __invoke(): void
    {
        logger()->debug('Start to execute action: ' . self::class);

        $userDto = UserState::load(bot()->userId());

        if (!empty($userDto->actions[self::CODE]) && $userDto->actions[self::CODE]->finished === false) {
            bot()->sendMessage("Вы уже запустили таймер!");
            logger()->debug('Action ' . self::class . ' skipped');
            return;
        }

        $startDate = now()->addSeconds(config('calendar.actions.work.pause_duration', 5));
        $time = Carbon::make($startDate)->setTimezone(config('app.timezone'));
        bot()->sendMessage("В $time отдых. Я напомню");

        $action = new ActionStateDto(
            self::class,
            false,
            now(),
            $startDate,
            self::CODE,
            self::TITLE,
        );

        dispatch(new TelegramActionJob(
            bot()->chatId(),
            bot()->userId(),
            'Пора отдыхать',
            $action,
            self::CODE . '_' . bot()->userId()
        ))->delay($startDate);

        UserState::changeAction(bot()->userId(), $action);

        logger()->debug('Success execute action: ' . self::class);
    }
}
