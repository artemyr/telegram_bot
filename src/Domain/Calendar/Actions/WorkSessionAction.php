<?php

namespace Domain\Calendar\Actions;

use App\Jobs\NotificationJob;
use Domain\Calendar\Models\Timer;
use Illuminate\Support\Carbon;

class WorkSessionAction
{
    public const TITLE = 'Таймер трудовой сессии';
    public const CODE = 'work_session';

    public function __invoke(): void
    {
        logger()->debug('Start to execute action: ' . self::class);

        $userDto = tuser();

        $timer = Timer::query()
            ->where('telegram_user_id', $userDto->userId)
            ->where('code', self::CODE)
            ->first();

        if ($timer && $timer->active) {
            message("Вы уже запустили таймер!");
            tuserstate()->changeBlockEditBotMessage(true);
            logger()->debug('Action ' . self::class . ' skipped');
            return;
        }

        $timer = Timer::query()
            ->where('telegram_user_id', $userDto->userId)
            ->where('code', self::CODE)
            ->withTrashed()
            ->first();

        $startDate = now()->addSeconds(5);

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
            ->delay($startDate->addSeconds(5));

        $time = Carbon::make($startDate)->setTimezone(tusertimezone());
        message("В $time отдых. Я напомню");
        tuserstate()->changeBlockEditBotMessage(true);


        logger()->debug('Success execute action: ' . self::class);
    }
}
