<?php

namespace App\Telegram\Middleware;

use Domain\TelegramBot\Models\TelegramUser;
use Illuminate\Support\Facades\Hash;
use SergiX44\Nutgram\Nutgram;

class AuthMiddleware
{
    public function __invoke(Nutgram $bot, $next): void
    {
        $botUser = $bot->user();
        $text = $bot->message()?->getText();

        if (!empty($text) && $text !== '/start') {
            $password = $text;
        }

        $tuser = TelegramUser::query()->where('telegram_id', $botUser?->id)->first();

        if (empty($tuser) && empty($password)) {
            logger()->alert(
                sprintf(
                    "Some user try to access bot:\n"
                    . "user_id: '%s', \n"
                    . "username: '%s', \n"
                    . "name: %s",
                    $botUser?->id,
                    $botUser?->username,
                    $botUser?->first_name .' '. $botUser?->last_name,
                )
            );

            bot()->sendMessage("Я вас еще не знаю\nВведите пароль для регистрации");

            return;
        }

        if (empty($tuser) && !Hash::check($password, config("auth.telegram.register_pass"))) {
            logger()->alert(
                sprintf(
                    "Some user try to access bot with password:\n"
                    . "user_id: '%s', \n"
                    . "username: '%s', \n"
                    . "name: '%s', \n"
                    . "password: %s",
                    $botUser?->id,
                    $botUser?->username,
                    $botUser?->first_name .' '. $botUser?->last_name,
                    $password
                )
            );

            bot()->sendMessage("Пароль не верный!");

            return;
        }

        if (empty($tuser)) {
            $tuser = new TelegramUser();
            $tuser->telegram_id = $botUser->id;
            $tuser->save();
        }

        $next($bot);
    }
}
