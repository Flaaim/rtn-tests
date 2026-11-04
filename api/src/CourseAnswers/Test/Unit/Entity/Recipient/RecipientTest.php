<?php

declare(strict_types=1);

namespace App\CourseAnswers\Test\Unit\Entity\Recipient;

use App\CourseAnswers\Entity\Recipient\Email;
use App\CourseAnswers\Entity\Recipient\Recipient;
use App\CourseAnswers\Entity\Recipient\RecipientId;
use App\CourseAnswers\Entity\Recipient\Status;
use App\CourseAnswers\Event\RecipientCreated;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class RecipientTest extends TestCase
{
    public function testRecipient(): void
    {
        $recipient = new Recipient(
            $recipientId = RecipientId::generate(),
            $productId = 'd3392276-946a-4dcf-9f41-c4e90aa3c8de',
            $email = new Email('test@email.ru'),
            $status = Status::PENDING,
            $sentAt = new DateTimeImmutable(),
        );

        self::assertEquals($recipientId, $recipient->getRecipientId());
        self::assertEquals($productId, $recipient->getProductId());
        self::assertEquals($email, $recipient->getEmail());
        self::assertEquals($status, $recipient->getStatus());
        self::assertEquals($sentAt, $recipient->getSendAt());

        $messages = $recipient->releaseEvents();
        $message = end($messages);
        self::assertInstanceOf(RecipientCreated::class, $message);
    }
}
