<?php

namespace App\Providers;

use Carbon\CarbonInterval;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Services\HumanDateParser\Parser;
use Services\Telegram\TelegramBotApi;
use Services\Telegram\TelegramBotApiContract;
use Support\Contracts\HumanDateParserContract;

class AppServiceProvider extends ServiceProvider
{
    public $bindings = [
        TelegramBotApiContract::class => TelegramBotApi::class,
        HumanDateParserContract::class => Parser::class,
    ];

    public function boot(): void
    {
        Model::shouldBeStrict(! app()->isProduction());
        /**
         * защита от проблемы ленивой загрузки отношений N+1
         * когда отношения модели подгружаются автоматически без явного указания
         * отножения нужно будет указывать явно, например так Post::with('author')->get()
         */
        Model::preventLazyLoading(! app()->isProduction());
        /**
         * в локальной разработке выдавать ошибку, если пытаемся
         * записать данные в защищенное поле модели
         */
        Model::preventSilentlyDiscardingAttributes(! app()->isProduction());

        if (app()->isProduction()) {

            /**
             * если запрос в бд слишком долго обрабатывается отправляем лог в телеграм
             */
            DB::listen(function ($query) {
                if ($query->time > 100) {
                    logger()
                        ->channel('telegram')
                        ->debug("query longer then {$query->time}ms: $query->sql");
                }
            });

            /**
             * если запрос слишком долго отрабатывает, то отправляем лог в телеграмл
             */
            app(Kernel::class)->whenRequestLifecycleIsLongerThan(
                CarbonInterval::seconds(4),
                function () {
                    logger()
                        ->channel('telegram')
                        ->debug('whenRequestLifecycleIsLongerThan:' . request()->url());
                }
            );
        }

        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(30)
                ->by($request->user()?->id() ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response('Too many requests.', Response::HTTP_TOO_MANY_REQUESTS, $headers);
                });
        });

        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id() ?: $request->ip());
        });
    }
}
