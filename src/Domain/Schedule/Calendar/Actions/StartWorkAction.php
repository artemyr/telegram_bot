<?php

namespace Domain\Schedule\Calendar\Actions;

use Domain\Schedule\Calendar\Models\Timer;
use Domain\Schedule\Jobs\NotificationJob;
use Illuminate\Support\Carbon;

class StartWorkAction
{
    public const TITLE = 'Рабочий таймер';
    public const CODE = 'start_work';

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
                ->text("Вы уже запустили рабочий день!")
                ->userId($this->tUserId)
                ->send();
            return;
        }

        $timer = Timer::query()
            ->where('telegram_user_id', $this->tUserId)
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
                'telegram_user_id' => $this->tUserId,
                'code' => self::CODE,
                'class' => self::class,
                'startDate' => $startDate,
                'title' => self::TITLE,
            ]);
        }

        dispatch(new NotificationJob(Timer::class, $timer->id, 'Пора завершать рабочий день!'))
            ->delay($startDate);

        $time = Carbon::make($startDate)->setTimezone(tusertimezone());
        message()
            ->text("Вы начали рабочий день. Напомню вам когда его нужно будет завершить. В $time")
            ->userId($this->tUserId)
            ->send();
    }
}
