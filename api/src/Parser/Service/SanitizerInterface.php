<?php

declare(strict_types=1);

namespace App\Parser\Service;

interface SanitizerInterface
{
    public function cleanTextContent(string $content): string;
}
