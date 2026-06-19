<?php

declare(strict_types=1);

namespace App\Parser\Service;

use DomainException;
use Webmozart\Assert\Assert;

final class GlueCookie
{
    public function glue(array $cookies): string
    {
        Assert::notEmpty($cookies);
        if (isset($cookies[2], $cookies[3], $cookies[0])) {
            return $cookies[2] . ' ' . $cookies[3] . ' ' . $cookies[0];
        }
        throw new DomainException('Glue cookie not supported');
    }
}
