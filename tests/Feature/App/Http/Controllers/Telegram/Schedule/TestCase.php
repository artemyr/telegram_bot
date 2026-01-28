<?php

namespace Tests\Feature\App\Http\Controllers\Telegram\Schedule;

abstract class TestCase extends \Tests\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        init_bot('schedule');
    }
}
