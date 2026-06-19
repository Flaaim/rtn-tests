<?php

declare(strict_types=1);

namespace App\Parser\Entity\Parser;

use Webmozart\Assert\Assert;

final class Host
{
    public function __construct(
        private string $value,
    ){
        Assert::notEmpty($this->value);
        Assert::true((bool) filter_var($value, FILTER_VALIDATE_URL), 'Неверный формат URL');
        $this->value = trim($this->value, '/');
    }

    public function getValue(): string
    {
        return $this->value;
    }

}
