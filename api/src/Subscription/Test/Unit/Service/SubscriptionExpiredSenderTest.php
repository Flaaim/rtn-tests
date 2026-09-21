<?php

declare(strict_types=1);

namespace App\Subscription\Test\Unit\Service;

use App\Subscription\Service\SubscriptionExpiredSender;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email as SymfonyEmail;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

/**
 * @internal
 * @coversNothing
 */
final class SubscriptionExpiredSenderTest extends TestCase
{
    public function testSuccess(): void
    {
        $to = 'test@email.ru';
        $template = 'subscribe/payment/expired.html.twig';

        $loader = new ArrayLoader([
            $template => '<p>to: {email}</p>',
        ]);
        $twig = new Environment($loader);

        $symfonyEmail = new SymfonyEmail()
            ->to($to)
            ->subject('Срок действия подписки закончился.')
            ->html($twig->render($template, ['email' => $to]));

        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects(self::once())->method('send')
            ->willReturnCallback(static function (SymfonyEmail $message) use ($symfonyEmail): int {
                self::assertEquals($symfonyEmail->getTo(), $message->getTo());
                self::assertEquals($symfonyEmail->getSubject(), $message->getSubject());
                self::assertStringContainsString((string)$symfonyEmail->getHtmlBody(), (string)$message->getHtmlBody());
                return 1;
            });

        $sender = new SubscriptionExpiredSender($mailer, $twig);

        $sender->send($to);
    }

    public function testError(): void
    {
        $to = 'test@emaile.ru';
        $template = 'subscribe/payment/expired.html.twig';

        $loader = new ArrayLoader([
            $template => '<p>to: {email}</p>',
        ]);
        $twig = new Environment($loader);

        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects(self::once())->method('send')->willThrowException(new TransportException('Transport failed'));

        $sender = new SubscriptionExpiredSender($mailer, $twig);

        self::expectException(TransportException::class);
        $sender->send($to);
    }
}
