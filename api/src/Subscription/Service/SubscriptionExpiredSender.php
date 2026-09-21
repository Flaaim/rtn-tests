<?php

declare(strict_types=1);

namespace App\Subscription\Service;

use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email as SymfonyEmail;
use Twig\Environment;

final readonly class SubscriptionExpiredSender
{
    public const string TEMPLATE = 'subscribe/payment/expired.html.twig';
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private MailerInterface $mailer,
        private Environment $twig
    ) {}
    public function send(string $email): void
    {
        $message = new SymfonyEmail()
            ->subject('Срок действия подписки закончился.')
            ->to($email)
            ->html($this->twig->render(self::TEMPLATE, ['email' => $email]));
        try {
            $this->mailer->send($message);
        } catch (TransportExceptionInterface $e) {
            throw new TransportException($e->getMessage());
        }
    }
}
