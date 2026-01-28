<?php

namespace Tests\Feature\App\Http\Controllers\Telegram\Schedule;

use App\Http\Controllers\Telegram\Schedule\StartController;

class StartControllerTest extends TestCase
{
    public function test_start_command_work(): void
    {
        app()->call(StartController::class);
        $log = message()->getLog();
        $this->assertTrue(in_array('Главное меню', $log));
    }
}
