<?php

namespace App\Telegram\Middleware;

use Domain\TelegramBot\Facades\UserState;
use Domain\TelegramBot\MenuBotState;
use Domain\TelegramBot\Models\TelegramUser;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\User\User;

class AuthMiddleware
{
    private ?User $botUser;
    private ?string $text;

    public function __invoke(Nutgram $bot, $next): void
    {
        $this->botUser = $bot->user();
        $this->text = $bot->message()?->getText();

        $tuser = TelegramUser::query()
            ->where('telegram_id', $this->botUser?->id)
            ->first();

        // если пользователя нет в базе
        if (empty($tuser)) {
            $this->newUserHandler();
        }

        // если есть в базе
        if (!empty($tuser)) {
            $this->existsUserHandler($tuser);
        }

        // на этот момент пользователь уже должен быть и в базе и в кеше
        $userDto = tuser();
        if (!$userDto) {
            throw new RuntimeException('User init error');
        }

        $tuser = TelegramUser::query()
            ->where('telegram_id', $userDto->userId)
            ->first();

        if (empty($tuser)) {
            throw new RuntimeException('User init error');
        }

        $next($bot);
    }

    private function requestOfPassword(): void
    {
        logger()->alert(
            sprintf(
                "Some user try to access bot:\n"
                . "user_id: '%s', \n"
                . "username: '%s', \n"
                . "name: %s",
                $this->botUser?->id,
                $this->botUser?->username,
                $this->botUser?->first_name . ' ' . $this->botUser?->last_name,
            )
        );

        send("Я вас еще не знаю\nВведите пароль для регистрации");
    }

    private function incorrectPassword(): void
    {
        logger()->alert(
            sprintf(
                "Some user try to access bot with password:\n"
                . "user_id: '%s', \n"
                . "username: '%s', \n"
                . "name: '%s', \n"
                . "password: %s",
                $this->botUser?->id,
                $this->botUser?->username,
                $this->botUser?->first_name . ' ' . $this->botUser?->last_name,
                $this->text
            )
        );

        send("Пароль не верный!");
    }

    private function newUserHandler(): void
    {
        // запрос пароля
        if ((empty($this->text) || $this->text === '/start')) {
            $this->requestOfPassword();
            exit();
        }

        // пароль не верный
        if (!Hash::check($this->text, config("telegram_bot.auth.register_pass"))) {
            $this->incorrectPassword();
            exit();
        }

        // создаем пользователя в базе
        $tuser = $this->createDatabaseUser();
        // создаем в кеше
        $this->createCacheUser($tuser);

        send("Вы зарегистрированы!");
        send("Обратите внимание на часовой пояс в настройках");
    }

    private function existsUserHandler(TelegramUser $tuser): void
    {
        $userDto = tuser();

        // если есть в кеше
        if ($userDto) {
            // то ничего не надо
            return;
        }

        // если есть в бд но нет в кеше - создаем
        if (!$userDto) {
            $this->createCacheUser($tuser);
            send("Вы долго не заходили ко мне. Ваше состояние потеряно. Начните сначала");
        }
    }

    private function createCacheUser(TelegramUser $tuser): void
    {
        $userDto = UserState::make(
            $tuser->telegram_id,
            new MenuBotState(troute('home')),
        );

        UserState::write($userDto);
    }

    private function createDatabaseUser(): TelegramUser
    {
        $tuser = new TelegramUser();
        $tuser->telegram_id = $this->botUser->id;
        $tuser->save();
        return $tuser;
    }
}
