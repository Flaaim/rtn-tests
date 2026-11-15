<?php

declare(strict_types=1);

namespace App\Profile\Command\Remove;

use App\Infrastructure\Doctrine\Flusher;
use App\Profile\Entity\Profile\ProfileId;
use App\Profile\Entity\Profile\ProfileRepository;

final class Handler
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private readonly ProfileRepository $profiles,
        private readonly Flusher $flusher
    ) {}

    public function handle(Command $command): void
    {
        $profile = $this->profiles->get(new ProfileId($command->id));

        $profile->remove();

        $this->profiles->remove($profile);

        $this->flusher->flush();
    }
}
