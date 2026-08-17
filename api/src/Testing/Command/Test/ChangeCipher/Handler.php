<?php

declare(strict_types=1);

namespace App\Testing\Command\Test\ChangeCipher;

use App\Infrastructure\Doctrine\Flusher;
use App\Testing\Entity\Test\TestId;
use App\Testing\Entity\Test\TestRepository;
use App\Testing\Service\SlugGeneratorInterface;
use DomainException;

final class Handler
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private readonly TestRepository $tests,
        private readonly SlugGeneratorInterface $slugGenerator,
        private readonly Flusher $flusher
    ) {}

    public function handle(Command $command): void
    {
        $newSlug = $this->slugGenerator->generate($command->cipher);

        $currentTest = $this->tests->get(new TestId($command->id));

        if ($this->tests->hasBySlug($newSlug) && $currentTest->getSlug() !== $newSlug) {
            throw new DomainException('Test with this cipher/slug already exists.');
        }

        $currentTest->changeCipher(
            $command->cipher,
            $newSlug
        );

        $this->flusher->flush();
    }
}
