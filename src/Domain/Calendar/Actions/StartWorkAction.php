<?php

namespace Domain\Calendar\Actions;

use App\Jobs\WorkSession;
use Domain\TelegramBot\Dto\ActionStateDto;
use Domain\TelegramBot\Facades\UserState;
use Illuminate\Support\Carbon;

class StartWorkAction
{
    public const TITLE = 'Рабочий таймер';
    public const CODE = 'start_work';

    public function __invoke(): void
    {
        $userDto = UserState::load(bot()->userId());

        if (!empty($userDto->actions[self::CODE]) && $userDto->actions[self::CODE]->finished === false) {
            bot()->sendMessage("Вы уже запустили рабочий день!");
            return;
        }

        $hour = config('calendar.actions.work.start_work');
        $startDate = now()->addHours($hour);

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

        dispatch(new WorkSession(
            bot()->chatId(),
            bot()->userId(),
            'Пора завершать рабочий день!',
            $action,
            self::CODE . '_' . bot()->userId()
        ))->delay($startDate);

        UserState::changeAction(bot()->userId(), $action);
    }
}
