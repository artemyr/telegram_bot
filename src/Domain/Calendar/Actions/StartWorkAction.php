<?php

namespace Domain\Calendar\Actions;

use App\Jobs\TelegramTimerJob;
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

        if (!empty($timer)) {
            bot()->sendMessage("Вы уже запустили рабочий день!");
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
            'timeout'
        ))->delay($startDate);

        $time = Carbon::make($startDate)->setTimezone(config('app.timezone'));
        bot()->sendMessage("Вы начали рабочий день. Напомню вам когда его нужно будет завершить. В $time");

        logger()->debug('Success execute action: ' . self::class);
    }

    public function timeout(int $chatId, int $timerId): void
    {
        bot()->sendMessage(
            text: 'Пора завершать рабочий день!',
            chat_id: $chatId,
        );

        Timer::query()
            ->where('id', $timerId)
            ->delete();
    }
}
