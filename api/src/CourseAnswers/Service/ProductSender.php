<?php

declare(strict_types=1);

namespace App\CourseAnswers\Service;

use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email as SymfonyEmail;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\File;
use Twig\Environment;

final readonly class ProductSender
{
    public const string TEMPLATE = 'product/mail.html.twig';

    public function __construct(
        private MailerInterface $mailer,
        private Environment $twig
    ) {}

    public function send(string $email, string $filePath, string $cipher): void
    {
        $message = new SymfonyEmail()
            ->subject('Ответы на вопросы тестирования.')
            ->to($email)
            ->html($this->twig->render(self::TEMPLATE, ['cipher' => $cipher]));

        $message->addPart(
            new DataPart(
                new File($filePath)
            )
        );
        try {
            $this->mailer->send($message);
        } catch (TransportExceptionInterface $e) {
            throw new TransportException($e->getMessage());
        }
    }
}
