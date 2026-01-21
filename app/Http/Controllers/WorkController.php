<?php

namespace App\Http\Controllers;

use Domain\TelegramBot\Models\TelegramUser;
use Illuminate\Support\Facades\Cache;

class WorkController extends Controller
{
    public function config()
    {
        $res = [
            'start' => false,
            'end' => false,
        ];

        $start = Cache::get('start_day');
        $end = Cache::get('end_day');

        if ($start === true && $end === true) {
            $start = false;
            Cache::set('start_day', false);
        }

        if ($start === true) {
            $res['start'] = true;

            $user = TelegramUser::query()
                ->select(['telegram_id'])
                ->where('id', 1)
                ->first();

            message()
                ->userId($user->telegram_id)
                ->text('Рабочий день начат')
                ->send();

            Cache::set('start_day', false);
        }

        if ($end === true) {
            $res['end'] = true;

            $user = TelegramUser::query()
                ->select(['telegram_id'])
                ->where('id', 1)
                ->first();

            message()
                ->userId($user->telegram_id)
                ->text('Рабочий день закончен')
                ->send();

            Cache::set('end_day', false);
        }

        return response()->json($res);
    }
}
