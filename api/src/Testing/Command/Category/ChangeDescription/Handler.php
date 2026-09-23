<?php

declare(strict_types=1);

namespace App\Testing\Command\Category\ChangeDescription;

use App\Infrastructure\Doctrine\Flusher;
use App\Testing\Entity\Category\CategoryId;
use App\Testing\Entity\Category\CategoryRepository;

final readonly class Handler
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private CategoryRepository $categories,
        private Flusher $flusher
    ) {}

    public function handle(Command $command): void
    {
        $category = $this->categories->getById(new CategoryId($command->id));

        $category->changeDescription($command->description);

        $this->flusher->flush();
    }
}
