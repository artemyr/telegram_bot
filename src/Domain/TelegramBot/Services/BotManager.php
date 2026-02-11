<?php

namespace Domain\TelegramBot\Services;

use Domain\TelegramBot\Contracts\BotContract;
use Domain\TelegramBot\Contracts\BotInstanceContract;
use Domain\TelegramBot\Contracts\UserInstanceContract;
use Domain\TelegramBot\Contracts\UserStateContract;
use Domain\TelegramBot\Factory\AbstractBotFactory;
use Nutgram\Laravel\RunningMode\LaravelWebhook;
use SergiX44\Nutgram\Nutgram;

class BotManager implements BotContract
{
    protected Nutgram $bot;
    protected string $botName;

    public function __construct(
        protected AbstractBotFactory $factory,
        protected bool $poling = false
    ) {
        $this->botName = $this->factory->getBotCode();
        $this->bootstrapBot();
    }

    public function current(): Nutgram
    {
        return $this->bot;
    }

    public function role(): string
    {
        return $this->botName;
    }

    private function bootstrapBot(): void
    {
        if ($this->poling) {
            // чтобы сработали провайдеры и подключился telegram.php - для локальной разработки
            $bot = app(Nutgram::class);
            $this->bot = app()->instance(BotInstanceContract::class, $bot);
        } else {
            $bot = new Nutgram(config("telegram_bot.bots.$this->botName.token"));
            $bot->setRunningMode(LaravelWebhook::class);
            $this->bot = app()->instance(BotInstanceContract::class, $bot);
            $this->factory->handle();
        }

        /** @var UserStateContract $userState */
        $userState = app(UserStateContract::class);
        $userState->setBotName($this->botName);
        app()->instance(UserInstanceContract::class, $userState);
    }
}
