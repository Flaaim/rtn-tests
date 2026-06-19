<?php

declare(strict_types=1);

namespace App\Parser\Entity\Parser;

use Webmozart\Assert\Assert;

final class Cookie
{
    private string $cookie;
    public function __construct(string|array $value)
    {
        Assert::notEmpty($value);
        if(is_string($value)) {
            $this->cookie = $value;
        }
        if(is_array($value)) {
            $this->cookie = $this->glueCookie($value);
        }
    }
    public function getCookie(): string
    {
        return $this->cookie;
    }
    private function glueCookie(array $cookies): string
    {
        if(isset($cookies[2]) && isset($cookies[3]) && isset($cookies[0])) {
            return $cookies[2] . ' ' . $cookies[3] . ' ' . $cookies[0];
        }
        throw new \DomainException('Glue cookie not supported');
    }
}
