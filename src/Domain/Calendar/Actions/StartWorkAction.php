<?php

namespace Domain\Calendar\Actions;

use App\Jobs\TelegramActionJob;
use Domain\TelegramBot\Dto\ActionStateDto;
use Domain\TelegramBot\Facades\UserState;
use Illuminate\Support\Carbon;

class StartWorkAction
{
    public const TITLE = 'Рабочий таймер';
    public const CODE = 'start_work';

    public function __invoke(): void
    {
        logger()->debug('Start to execute action: ' . self::class);

        $userDto = tuser();

        if (!empty($userDto->actions[self::CODE]) && $userDto->actions[self::CODE]->finished === false) {
            bot()->sendMessage("Вы уже запустили рабочий день!");
            logger()->debug('Action ' . self::class . ' skipped');
            return;
        }

        $startDate = now()->addSeconds(config('calendar.actions.work.start_work', 5));
        $time = Carbon::make($startDate)->setTimezone(config('app.timezone'));
        bot()->sendMessage("Вы начали рабочий день. Напомню вам когда его нужно будет завершить. В $time");

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
            'Пора завершать рабочий день!',
            $action,
            self::CODE . '_' . bot()->userId()
        ))->delay($startDate);

        UserState::changeAction(bot()->userId(), $action);

        logger()->debug('Success execute action: ' . self::class);
    }
}
