<?php

namespace Domain\TelegramBot\Services;

use App\Telegram\Contracts\BotContract;
use Domain\TelegramBot\Exceptions\TelegramBotException;
use SergiX44\Nutgram\Nutgram;

class BotManager implements BotContract
{
    protected Nutgram $bot;
    protected array $bots;

    public function __construct(Nutgram $bot)
    {
        $this->bot = $bot;
        $this->bots = config('nutgram.bots');
    }

    public function current(): Nutgram
    {
        return $this->bot;
    }

    public function username(): string
    {
        return $this->bot->getMe()->username;
    }

    /**
     * @throws TelegramBotException
     */
    public function role(): string
    {
        $username = $this->username();
        foreach ($this->bots as $role => $bot) {
            if ($bot['username'] === $username) {
                return $role;
            }
        }

        throw new TelegramBotException('Unknown bot');
    }
}
