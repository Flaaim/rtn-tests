<?php

declare(strict_types=1);

namespace App\Subscription\Entity\Payment;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Embeddable]
final class Amount
{
    public function __construct(
        #[ORM\Column(name: 'amount', type: 'string', length: 16)]
        private string $value,
        #[ORM\Column(name: 'currency', type: 'string', length: 3)]
        private string $currency = 'RUB',
    ) {
        Assert::regex($value, '/^\d+\.\d{2}$/', 'Amount must have two decimal places, e.g. 490.00');
        Assert::lengthBetween($currency, 3, 3);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function isEqualTo(self $other): bool
    {
        return $this->value === $other->value && $this->currency === $other->currency;
    }
}
