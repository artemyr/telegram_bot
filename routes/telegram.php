<?php
/** @var SergiX44\Nutgram\Nutgram $bot */

use App\Console\Commands\Telegram\CancelCommand;
use App\Console\Commands\Telegram\StartCommand;
use SergiX44\Nutgram\Nutgram;

/*
|--------------------------------------------------------------------------
| Nutgram Handlers
|--------------------------------------------------------------------------
|
| Here is where you can register telegram handlers for Nutgram. These
| handlers are loaded by the NutgramServiceProvider. Enjoy!
|
*/

$menu = new \Services\TelegramBot\Bot();
$menu($bot);

if (app()->isLocal()) {
    $bot->registerMyCommands();
}
