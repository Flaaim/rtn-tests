<?php

declare(strict_types=1);

namespace App\Course\Test\Unit\Entity\Course\Command\MarkAsCreated;

use App\Course\Entity\Course\Status;
use App\Course\Event\CourseScheduledToRender;
use App\Course\Test\Builder\CourseBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class MarkAsCreatedTest extends TestCase
{
    public function testCompleteCreating(): void
    {
        $course = new CourseBuilder()
            ->build();

        $course->markAsCreated();

        self::assertEquals(Status::created(), $course->getStatus());

        $messages = $course->releaseEvents();
        self::assertCount(1, $messages);

        $message = $messages[0];
        self::assertInstanceOf(CourseScheduledToRender::class, $message);

        self::assertEquals($course->getCourseId()->getValue(), $message->courseId);
    }
}
