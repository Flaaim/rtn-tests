<?php

declare(strict_types=1);

namespace App\Auth\Command\ForceChangePassword;

use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\UserRepository;
use App\Auth\Service\PasswordHasher;
use App\Infrastructure\Doctrine\Flusher;

final readonly class Handler
{
    public function __construct(
        private UserRepository $users,
        private Flusher $flusher,
        private PasswordHasher $hasher,
    ) {}

    public function handle(Command $command): void
    {
        $user = $this->users->get(new Id($command->id));

        $passwordHash = $this->hasher->hash($command->password);

        $user->forceChangePassword($passwordHash);

        $this->flusher->flush();
    }
}
