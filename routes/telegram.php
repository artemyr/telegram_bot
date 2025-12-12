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

$bot->registerCommand(StartCommand::class);
$bot->registerCommand(CancelCommand::class);

if (app()->isLocal()) {
    $bot->registerMyCommands();
}
