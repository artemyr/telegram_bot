<?php

namespace Tests\Feature\Jobs\Tasks\Recurrence;

use App\Jobs\NotificationJob;
use App\Jobs\Tasks\Recurrence\GenerateTaskOccurrencesJob;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class GenerateTaskOccurrencesJobTest extends TestCase
{
    public function test_it_recurrence_task_notification_generate_ok()
    {
        Queue::fake([NotificationJob::class]);
        GenerateTaskOccurrencesJob::dispatchSync();
        Queue::assertPushed(NotificationJob::class, 8);
    }
}
