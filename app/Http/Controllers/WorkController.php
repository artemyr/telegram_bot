<?php

namespace App\Http\Controllers;

use Domain\TelegramBot\Models\TelegramUser;
use Illuminate\Support\Facades\Cache;

class WorkController extends Controller
{
    public function __construct()
    {
        init_bot('schedule');
    }

    public function config()
    {
        $res = [
            'start' => false,
            'end' => false,
            'test' => false,
        ];

        $start = Cache::get('start_day');
        $end = Cache::get('end_day');
        $test = Cache::get('work_test');

        Cache::set('start_day', false);
        Cache::set('end_day', false);
        Cache::set('work_test', false);

        if ($start === true && $end === true) {
            $start = false;
            Cache::set('start_day', false);
        }

        if ($start === true) {
            $res['start'] = true;
        }

        if ($end === true) {
            $res['end'] = true;
        }

        if ($test === true) {
            $res['test'] = true;
        }

        return response()->json($res);
    }

    public function result($action)
    {
        match ($action) {
            'start' => $this->start(),
            'end' => $this->end(),
            'test' => $this->test(),
            'error' => $this->error(),
            default => false,
        };
    }

    protected function start()
    {
        $user = $this->getUser();

        if (empty($user)) {
            return;
        }

        message()
            ->userId($user->telegram_id)
            ->text('Рабочий день начат')
            ->send();
    }

    protected function end()
    {
        $user = $this->getUser();

        if (empty($user)) {
            return;
        }
        message()
            ->userId($user->telegram_id)
            ->text('Рабочий день закончен')
            ->send();
    }

    protected function test()
    {
        $user = $this->getUser();

        if (empty($user)) {
            return;
        }

        message()
            ->userId($user->telegram_id)
            ->text('Тест')
            ->send();
    }

    protected function error()
    {
        $user = $this->getUser();

        if (empty($user)) {
            return;
        }

        message()
            ->userId($user->telegram_id)
            ->text('Ошибка')
            ->send();
    }

    protected function getUser(): ?TelegramUser
    {
        return TelegramUser::query()
            ->select(['telegram_id'])
            ->where('id', 1)
            ->first();
    }
}
