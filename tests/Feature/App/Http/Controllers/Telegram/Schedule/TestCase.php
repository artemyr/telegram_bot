<?php

namespace Tests\Feature\App\Http\Controllers\Telegram\Schedule;

use Domain\Schedule\Factory\ScheduleBotFactory;

abstract class TestCase extends \Tests\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        init_bot(ScheduleBotFactory::class);
    }
}
