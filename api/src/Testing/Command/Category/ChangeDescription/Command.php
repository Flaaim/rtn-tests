<?php

declare(strict_types=1);

namespace App\Testing\Command\Category\ChangeDescription;

use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $id,
        #[Assert\Length(min: 1, max: 255, maxMessage: 'Описание категории слишком длинное!')]
        public string $description,
    ) {}
}
