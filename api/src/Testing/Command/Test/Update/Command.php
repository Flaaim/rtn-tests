<?php

declare(strict_types=1);

namespace App\Testing\Command\Test\Update;

use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public readonly string $id,
        #[Assert\Count(min: 1)]
        #[Assert\All(
            new Assert\Uuid()
        )]
        public readonly array $courseIds
    ) {}
}
