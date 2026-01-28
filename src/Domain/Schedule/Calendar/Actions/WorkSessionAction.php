<?php

namespace Domain\Schedule\Calendar\Actions;

use Domain\Schedule\Calendar\Models\Timer;
use Domain\Schedule\Jobs\NotificationJob;
use Illuminate\Support\Carbon;

class WorkSessionAction
{
    public const TITLE = 'Таймер трудовой сессии';
    public const CODE = 'work_session';

    public function __invoke(): void
    {
        $userDto = tuser()->get();

        $timer = Timer::query()
            ->where('telegram_user_id', $userDto->userId)
            ->where('code', self::CODE)
            ->first();

        if ($timer && $timer->active) {
            message("Вы уже запустили таймер!");
            return;
        }

        $timer = Timer::query()
            ->where('telegram_user_id', $userDto->userId)
            ->where('code', self::CODE)
            ->withTrashed()
            ->first();

        $startDate = now()->addSeconds(config('calendar.actions.work.pause_duration', 5));

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

        dispatch(new NotificationJob(Timer::class, $timer->id, 'Пора отдыхать'))
            ->delay($startDate);

        dispatch(new NotificationJob(Timer::class, $timer->id, 'Можно начинать работать'))
            ->delay($startDate->addMinutes(10));

        $time = Carbon::make($startDate)->setTimezone(tusertimezone());
        message("В $time отдых. Я напомню");
    }
}
