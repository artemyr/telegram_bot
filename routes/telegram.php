<?php
/** @var SergiX44\Nutgram\Nutgram $bot */

/*
|--------------------------------------------------------------------------
| Nutgram Handlers
|--------------------------------------------------------------------------
|
| Here is where you can register telegram handlers for Nutgram. These
| handlers are loaded by the NutgramServiceProvider. Enjoy!
|
*/

use App\Telegram\Factory\ScheduleBotFactory;
use App\Telegram\Factory\TravelBotFactory;

/**
 * для работы в long-polling режиме
 * можно дебажить только одного бота
 * перед работой ввести актуальный token бота в env в TELEGRAM_TOKEN
 * a nutgram:run
 */

if (app()->isLocal()) {
    nutgram('test');
//    ScheduleBotFactory::run();
    TravelBotFactory::run();
}



