<?php

declare(strict_types=1);

namespace App\CourseAnswers\Test\Unit\Service;

use App\CourseAnswers\Service\ProductSender;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email as SymfonyEmail;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\File;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

/**
 * @internal
 * @coversNothing
 */
final class ProductSenderTest extends TestCase
{
    public function testSuccess(): void
    {
        $to = 'test@email.ru';
        $template = 'product/mail.html.twig';
        $filePath = 'file/path.pdf';
        $cipher = 'ОТ 201.18';

        $loader = new ArrayLoader([
            $template => '<p>Template file: {cipher}</p>',
        ]);
        $twig = new Environment($loader);

        $symfonyEmail = new SymfonyEmail()
            ->to($to)
            ->subject('Ответы на вопросы тестирования.')
            ->addPart(new DataPart(
                new File($filePath)
            ))
            ->html($twig->render($template, ['cipher' => $cipher]));

        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects(self::once())->method('send')
            ->willReturnCallback(static function (SymfonyEmail $message) use ($symfonyEmail): int {
                self::assertEquals($symfonyEmail->getTo(), $message->getTo());
                self::assertEquals($symfonyEmail->getSubject(), $message->getSubject());
                self::assertEquals($symfonyEmail->getAttachments(), $message->getAttachments());
                self::assertStringContainsString((string)$symfonyEmail->getHtmlBody(), (string)$message->getHtmlBody());
                return 1;
            });

        $sender = new ProductSender($mailer, $twig);

        $sender->send($to, $filePath, $cipher);
    }

    public function testError(): void
    {
        $to = 'test@email.ru';
        $template = 'product/mail.html.twig';
        $filePath = 'file/path.pdf';
        $cipher = 'ОТ 201.18';

        $loader = new ArrayLoader([
            $template => '<p>Template file: {cipher}</p>',
        ]);
        $twig = new Environment($loader);

        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects(self::once())->method('send')->willThrowException(new TransportException('Transport failed'));

        $sender = new ProductSender($mailer, $twig);
        self::expectException(TransportException::class);

        $sender->send($to, $filePath, $cipher);
    }
}
