<?php

declare(strict_types=1);

namespace App\Testing\Service;

interface SlugGeneratorInterface
{
    public function generate(string $value): string;
}
