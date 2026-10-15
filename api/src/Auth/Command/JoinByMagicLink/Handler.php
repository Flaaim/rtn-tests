<?php

declare(strict_types=1);

namespace App\Auth\Command\JoinByMagicLink;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\User;
use App\Auth\Entity\User\UserRepository;
use App\Auth\Service\Tokenizer;
use App\Infrastructure\Doctrine\Flusher;
use DateTimeImmutable;

final readonly class Handler
{
    public function __construct(
        private UserRepository $users,
        private Tokenizer $tokenizer,
        private Flusher $flusher
    ) {}

    public function handle(Command $command): void
    {
        $email = new Email($command->email);
        $date = new DateTimeImmutable();

        $user = $this->users->findByEmail($email);
        $token = $this->tokenizer->generate($date);

        if (null === $user) {
            $user = User::requestJoinByMagicLink(
                Id::generate(),
                $date,
                $email
            );

            $this->users->add($user);
        }

        $user->requestMagicLink($token, $date);

        $this->flusher->flush();
    }
}
