<?php

namespace Domain\Calendar\Actions;

use Domain\Calendar\Models\Timer;
use Illuminate\Support\Carbon;

class StartWorkAction
{
    public const TITLE = 'Рабочий таймер';
    public const CODE = 'start_work';

    public function __invoke(): void
    {
        logger()->debug('Start to execute action: ' . self::class);

        $userDto = tuser();

        $timer = Timer::query()
            ->where('telegram_user_id', $userDto->userId)
            ->where('code', self::CODE)
            ->first();

        if ($timer && $timer->active) {
            send("Вы уже запустили рабочий день!");
            logger()->debug('Action ' . self::class . ' skipped');
            return;
        }

        $timer = Timer::query()
            ->where('telegram_user_id', $userDto->userId)
            ->where('code', self::CODE)
            ->withTrashed()
            ->first();

        $startDate = now()->addSeconds(config('calendar.actions.work.start_work', 5));

        if (!empty($timer)) {
            if ($timer->trashed()) {
                $timer->restore();
            }
            $timer->update([
                'class' => self::class,
                'startDate' => $startDate,
                'title' => self::TITLE,
            ]);
        } else {
            $timer = Timer::create([
                'telegram_user_id' => $userDto->userId,
                'code' => self::CODE,
                'class' => self::class,
                'startDate' => $startDate,
                'title' => self::TITLE,
            ]);
        }

        $timer->notifications()->create([
            'date' => $startDate,
            'message' => 'Пора завершать рабочий день!',
        ]);

        $time = Carbon::make($startDate)->setTimezone(tusertimezone());
        send("Вы начали рабочий день. Напомню вам когда его нужно будет завершить. В $time");

        logger()->debug('Success execute action: ' . self::class);
    }
}
