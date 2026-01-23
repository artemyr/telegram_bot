<?php

namespace App\Telegram\Middleware;

use Domain\Schedule\Settings\Enums\TimezoneEnum;
use Domain\TelegramBot\Models\TelegramUser;
use SergiX44\Nutgram\Nutgram;

class CheckUserMiddleware
{
    public function __invoke(Nutgram $bot, $next): void
    {
        $tuser = TelegramUser::query()
            ->select(['timezone'])
            ->where('telegram_id', $bot->userId())
            ->first();

        if (!empty($tuser->getAttributes()['timezone'])) {
            $next($bot);
            return;
        }

        $text = $bot->message()?->getText();

        if (empty($text)) {
            $this->sendTimezones();
        } else {
            $res = $this->handleTimezones();

            if ($res) {
                $next($bot);
                return;
            }
        }
    }

    protected function sendTimezones(): void
    {
        $keyboard = [];
        foreach (TimezoneEnum::cases() as $case) {
            $keyboard[] = $case->value;
        }
        message()
            ->text('Установите ваш часовой пояс')
            ->replyKeyboard($keyboard)
            ->send();
    }

    protected function handleTimezones(): bool
    {
        foreach (TimezoneEnum::cases() as $case) {
            if (bot()->message()->getText() === $case->value) {
                message("Вы отметили: " . $case->value);
                TelegramUser::query()
                    ->where('telegram_id', bot()->userId())
                    ->update([
                        'timezone' => $case->value,
                    ]);
                return true;
            }
        }

        message('Выберите из списка!');
        $this->sendTimezones();
        return false;
    }
}
