<?php

declare(strict_types=1);

namespace App\CourseAnswers\Test\Unit\Entity\Recipient\Command\Sent;

use App\CourseAnswers\Test\Builder\RecipientBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class SentTest extends TestCase
{
    public function testSent(): void
    {
        $recipient = new RecipientBuilder()
            ->build();

        $recipient->sent();

        self::assertTrue($recipient->isSent());
    }
}
