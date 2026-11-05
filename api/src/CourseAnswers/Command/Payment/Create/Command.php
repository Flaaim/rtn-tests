<?php

declare(strict_types=1);

namespace App\CourseAnswers\Command\Payment\Create;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class Command
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $productId,
        #[Assert\Email]
        public string $email,
        #[Assert\NotBlank]
        public string $returnUrl
    ) {}
}
