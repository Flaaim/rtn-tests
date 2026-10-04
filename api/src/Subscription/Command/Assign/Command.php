<?php

declare(strict_types=1);

namespace App\Subscription\Command\Assign;

use App\Subscription\Entity\Subscription\Plan;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class Command
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $userId,
        #[Assert\Choice(choices: [Plan::TRIAL->value, Plan::BASIC->value])]
        public string $plan,
        #[Assert\NotBlank]
        #[Assert\DateTime(format: 'Y-m-d')]
        public string $periodStart,
        #[Assert\NotBlank]
        #[Assert\DateTime(format: 'Y-m-d')]
        public string $periodEnd,
    ) {}
}
