<?php

namespace Tests\Feature;

use Illuminate\Console\Scheduling\Schedule;
use Tests\TestCase;

class ConsoleScheduleTest extends TestCase
{
    public function test_update_pupil_progressions_is_scheduled_daily(): void
    {
        $event = collect(app(Schedule::class)->events())
            ->first(fn ($event) => str_contains($event->command, 'app:update-pupil-progressions'));

        $this->assertNotNull($event);
        $this->assertEquals('0 0 * * *', $event->getExpression());
    }
}
