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

$bot->onMessage(function (Nutgram $bot) {
    $m = $bot->message();
    $bot->sendMessage('You sent a message! ' . $m->getText());
});

$bot->onText('Give me food!', function (Nutgram $bot) {
    $bot->sendMessage('Apple!');
});

$bot->onText('Give me animal!', function (Nutgram $bot) {
    $bot->sendMessage('Dog!');
});

$bot->registerCommand(StartCommand::class);
$bot->registerCommand(CancelCommand::class);
