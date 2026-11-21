<?php

declare(strict_types=1);

namespace App\Course\Test\Unit\Entity\Course\Command\MarkAsRendered;

use App\Course\Entity\Course\Status;
use App\Course\Event\CourseRendered;
use App\Course\Test\Builder\CourseBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class MarkAsRenderedTest extends TestCase
{
    public function testMarkAsRendered(): void
    {
        $course = new CourseBuilder()
            ->build();

        $course->markAsRendered('test.pdf', '350.00');

        self::assertEquals(Status::completed(), $course->getStatus());

        $messages = $course->releaseEvents();
        self::assertCount(1, $messages);

        $message = $messages[0];

        self::assertInstanceOf(CourseRendered::class, $message);

        self::assertEquals($course->getCourseId()->getValue(), $message->courseId);
        self::assertEquals($course->getCipher(), $message->cipher);
        self::assertEquals('350.00', $message->amount);
        self::assertEquals('test.pdf', $message->filePath);
    }
}
