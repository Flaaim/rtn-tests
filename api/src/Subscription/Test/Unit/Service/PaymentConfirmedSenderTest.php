<?php

declare(strict_types=1);

namespace App\Subscription\Test\Unit\Service;

use App\Subscription\Service\PaymentConfirmedSender;
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
final class PaymentConfirmedSenderTest extends TestCase
{
    public function testSuccess(): void
    {
        $to = 'test@emaile.ru';
        $ended = '10.10.2026';

        $template = 'subscribe/payment/confirm.html.twig';

        $loader = new ArrayLoader([
            $template => '<p>to: {email} ended: {ended}</p>',
        ]);
        $twig = new Environment($loader);

        $symfonyEmail = new SymfonyEmail()
            ->to($to)
            ->subject('Подписка активирована.')
            ->html($twig->render($template, ['email' => $to, 'ended' => $ended]));

        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects(self::once())->method('send')
            ->willReturnCallback(static function (SymfonyEmail $message) use ($symfonyEmail): int {
                self::assertEquals($symfonyEmail->getTo(), $message->getTo());
                self::assertEquals($symfonyEmail->getSubject(), $message->getSubject());
                self::assertStringContainsString((string)$symfonyEmail->getHtmlBody(), (string)$message->getHtmlBody());
                return 1;
            });

        $sender = new PaymentConfirmedSender($mailer, $twig);

        $sender->send($to, $ended);
    }

    public function testError(): void
    {
        $to = 'test@emaile.ru';
        $ended = '10.10.2026';

        $template = 'subscribe/payment/confirm.html.twig';

        $loader = new ArrayLoader([
            $template => '<p>to: {email} ended: {ended}</p>',
        ]);
        $twig = new Environment($loader);

        $mailer = $this->createMock(MailerInterface::class);

        $mailer->expects(self::once())->method('send')->willThrowException(new TransportException('Transport failed'));

        $sender = new PaymentConfirmedSender($mailer, $twig);

        self::expectException(TransportException::class);
        $sender->send($to, $ended);
    }
}
