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

use App\Telegram\Middleware\AuthMiddleware;
use App\Telegram\Middleware\RequestMiddleware;
use Domain\Menu\MenuFactory;

$bot->middleware(AuthMiddleware::class);
$bot->middleware(RequestMiddleware::class);

$menu = new MenuFactory();
$menu($bot);

if (app()->isLocal()) {
    $bot->registerMyCommands();
}
