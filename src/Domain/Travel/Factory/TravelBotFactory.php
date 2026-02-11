<?php

namespace Domain\Travel\Factory;


use App\Http\Controllers\Telegram\Travel\CallbackStateController;
use App\Http\Controllers\Telegram\Travel\MessageStateController;
use App\Http\Controllers\Telegram\Travel\StartController;
use App\Menu\MenuContract;
use Domain\TelegramBot\Factory\AbstractBotFactory;
use Domain\TelegramBot\Middleware\AuthMiddleware;
use Domain\TelegramBot\Middleware\CheckUserMiddleware;

class TravelBotFactory extends AbstractBotFactory
{
    public function getBotCode(): string
    {
        return 'travel';
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
