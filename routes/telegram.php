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

use Domain\Menu\MenuFactory;

$menu = new MenuFactory();
$menu($bot);

if (app()->isLocal()) {
    $bot->registerMyCommands();
}
