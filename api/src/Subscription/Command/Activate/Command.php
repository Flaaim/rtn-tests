<?php

declare(strict_types=1);

namespace App\Subscription\Command\Activate;

use App\Subscription\Entity\Subscription\Plan;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class Command
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $userId,
        #[Assert\GreaterThan(0)]
        public int $durationDays,
        #[Assert\Choice(choices: [Plan::TRIAL->value, Plan::BASIC->value])]
        public string $plan,
    ) {}
}
