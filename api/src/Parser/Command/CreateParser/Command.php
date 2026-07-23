<?php

declare(strict_types=1);

namespace App\Parser\Command\CreateParser;

use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    public function __construct(
        #[Assert\Url(
            message: 'The url {{ value }} is not a valid url'
        )]
        #[Assert\NotBlank]
        public readonly string $host,
        #[Assert\NotBlank]
        public readonly string $login,
        #[Assert\NotBlank]
        public readonly string $password,
    ) {}
}
