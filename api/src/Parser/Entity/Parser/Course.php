<?php

declare(strict_types=1);

namespace App\Parser\Entity\Parser;

use Webmozart\Assert\Assert;

final class Course
{
    public function __construct(
        private readonly array $data
    ){
        Assert::notEmpty($this->data);
    }

    public function get(string $value): string
    {
        Assert::keyExists($this->data, $value);
        return $this->data[$value];
    }
}
