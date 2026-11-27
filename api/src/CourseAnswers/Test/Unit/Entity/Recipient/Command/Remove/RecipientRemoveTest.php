<?php

declare(strict_types=1);

namespace App\CourseAnswers\Test\Unit\Entity\Recipient\Command\Remove;

use App\CourseAnswers\Event\Recipient\RecipientRemoved;
use App\CourseAnswers\Test\Builder\RecipientBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class RecipientRemoveTest extends TestCase
{
    public function testRemove(): void
    {
        $recipient = new RecipientBuilder()
            ->build();

        $recipient->remove();

        $messages = $recipient->releaseEvents();

        self::assertCount(1, $messages);
        $message = $messages[0];

        self::assertInstanceOf(RecipientRemoved::class, $message);
        self::assertEquals($recipient->getRecipientId()->getValue(), $message->recipientId);
    }
}
