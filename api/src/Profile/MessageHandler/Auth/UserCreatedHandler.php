<?php

declare(strict_types=1);

namespace App\Profile\MessageHandler\Auth;

use App\Auth\Event\UserCreated;
use App\Infrastructure\Doctrine\Flusher;
use App\Profile\Entity\Profile\Email;
use App\Profile\Entity\Profile\Profile;
use App\Profile\Entity\Profile\ProfileId;
use App\Profile\Entity\Profile\ProfileRepository;
use App\Profile\Entity\Profile\Role;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/** @psalm-suppress UnusedClass */
#[AsMessageHandler]
final class UserCreatedHandler
{
    public function __construct(
        private readonly ProfileRepository $profiles,
        private readonly Flusher $flusher,
    ) {}

    public function __invoke(UserCreated $event): void
    {
        $email = new Email($event->email);
        if ($this->profiles->hasByEmail($email)) {
            return;
        }

        $profile = Profile::createFromSignUp(
            new ProfileId($event->id),
            $email,
            new Role($event->role),
        );

        $this->profiles->add($profile);

        $this->flusher->flush();
    }
}
