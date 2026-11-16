<?php

declare(strict_types=1);

namespace App\Profile\Command\Add;

use App\Infrastructure\Doctrine\Flusher;
use App\Profile\Entity\Profile\Email;
use App\Profile\Entity\Profile\Profile;
use App\Profile\Entity\Profile\ProfileId;
use App\Profile\Entity\Profile\ProfileRepository;
use App\Profile\Entity\Profile\Role;
use DomainException;

/** @psalm-suppress UnusedClass */
final readonly class Handler
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private ProfileRepository $profiles,
        private Flusher $flusher,
    ) {}

    public function handle(Command $command): void
    {
        $email = new Email($command->email);
        $role = new Role($command->role);
        if ($this->profiles->hasByEmail($email)) {
            throw new DomainException('This email already exists.');
        }

        $profile = Profile::createManually(
            ProfileId::generate(),
            $email,
            $role,
            $command->name,
            $command->surname
        );

        $this->profiles->add($profile);

        $this->flusher->flush();
    }
}
