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

/**
 * для работы в long-polling режиме
 * можно дебажить только одного бота
 * перед работой ввести актуальный token бота в env в TELEGRAM_TOKEN
 * a nutgram:run
 */

use Domain\Schedule\Factory\ScheduleBotFactory;
use Domain\Travel\Factory\TravelBotFactory;

if (app()->isLocal()) {
    $bot = init_bot(ScheduleBotFactory::class, true);
//    $bot = init_bot(TravelBotFactory::class, true);
}
