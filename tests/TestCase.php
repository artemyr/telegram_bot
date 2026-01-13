<?php

namespace Tests;

use Domain\TelegramBot\Services\MessageManager;
use Domain\TelegramBot\Services\UserStateManager;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Queue;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        MessageManager::fake();
        UserStateManager::fake();
        Queue::fake();
    }
}
