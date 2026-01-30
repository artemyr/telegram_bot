<?php

namespace Domain\Schedule\Calendar\Actions;

use Domain\Schedule\Calendar\Models\Timer;
use Domain\Schedule\Jobs\NotificationJob;
use Illuminate\Support\Carbon;

class WorkSessionAction
{
    public const TITLE = 'Таймер трудовой сессии';
    public const CODE = 'work_session';

    public function __construct(protected int $tUserId)
    {
    }

    public function __invoke(): void
    {
        $timer = Timer::query()
            ->where('telegram_user_id', $this->tUserId)
            ->where('code', self::CODE)
            ->first();

        if ($timer && $timer->active) {
            message()
                ->text("Вы уже запустили таймер!")
                ->userId($this->tUserId)
                ->send();
            return;
        }

        $timer = Timer::query()
            ->where('telegram_user_id', $this->tUserId)
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
                'telegram_user_id' => $this->tUserId,
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

        $time = Carbon::make($startDate)->setTimezone(tusertimezone($this->tUserId));
        message()
            ->text("В $time отдых. Я напомню")
            ->userId($this->tUserId)
            ->send();
    }
}
