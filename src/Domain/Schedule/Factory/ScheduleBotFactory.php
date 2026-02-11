<?php

namespace Domain\Schedule\Factory;

use App\Http\Controllers\Telegram\Schedule\CallbackStateController;
use App\Http\Controllers\Telegram\Schedule\MessageStateController;
use App\Http\Controllers\Telegram\Schedule\StartController;
use App\Menu\MenuContract;
use Domain\TelegramBot\Factory\AbstractBotFactory;
use Domain\TelegramBot\Middleware\AuthMiddleware;
use Domain\TelegramBot\Middleware\CheckUserMiddleware;

class ScheduleBotFactory extends AbstractBotFactory
{
    public function getBotCode(): string
    {
        return 'schedule';
    }

    public function handle(): void
    {
        $bot = nutgram();

        $menuFactory = config("telegram_bot.bots.{$this->getBotCode()}.menu");
        if (!empty($menuFactory)) {
            app()->instance(MenuContract::class, $menuFactory::create());
        }

        $bot->middleware(AuthMiddleware::class);
        $bot->middleware(CheckUserMiddleware::class);

        $bot->registerCommand(StartController::class);

        $bot->onMessage(MessageStateController::class);

        $bot->onCallbackQuery(CallbackStateController::class);
    }
}
