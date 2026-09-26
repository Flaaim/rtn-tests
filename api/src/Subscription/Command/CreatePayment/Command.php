<?php

declare(strict_types=1);

namespace App\Subscription\Command\CreatePayment;

use App\Subscription\Entity\Subscription\Plan;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class Command
{
    public function __construct(
        #[Assert\Choice(choices: [Plan::BASIC->value])]
        public string $plan,
        #[Assert\NotBlank]
        public string $amount,
        #[Assert\GreaterThan(0)]
        public int $durationDays,
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $userId,
        #[Assert\NotBlank]
        public string $returnUrl,
    ) {}
}
