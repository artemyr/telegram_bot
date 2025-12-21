<?php

namespace Domain\Calendar\Actions;

use App\Jobs\TelegramTimerJob;
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
            bot()->sendMessage("Вы уже запустили таймер!");
            logger()->debug('Action ' . self::class . ' skipped');
            return;
        }

        $timer = Timer::query()
            ->where('telegram_user_id', $userDto->userId)
            ->where('code', self::CODE)
            ->withTrashed()
            ->first();

        $startDate = now()->addSeconds(config('calendar.actions.work.pause_duration', 5));

        if (!empty($timer)) {
            $timer->restore();
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

        dispatch(new TelegramTimerJob(
            bot()->chatId(),
            bot()->userId(),
            $timer->id,
            self::class,
            'timeout',
        ))->delay($startDate);

        dispatch(new TelegramTimerJob(
            bot()->chatId(),
            bot()->userId(),
            $timer->id,
            self::class,
            'revoke',
        ))->delay($startDate->addMinutes(10));

        $time = Carbon::make($startDate)->setTimezone(tusertimezone());
        bot()->sendMessage("В $time отдых. Я напомню");

        logger()->debug('Success execute action: ' . self::class);
    }

    public function timeout(int $chatId, int $timerId): void
    {
        bot()->sendMessage(
            text: 'Пора отдыхать',
            chat_id: $chatId,
        );

        Timer::query()
            ->where('id', $timerId)
            ->delete();
    }

    public function revoke(int $chatId, int $timerId): void
    {
        bot()->sendMessage(
            text: 'Можно начинать работать',
            chat_id: $chatId,
        );
    }
}
