<?php

declare(strict_types=1);

namespace App\Testing\Service;

use DomainException;
use Symfony\Component\String\Slugger\AsciiSlugger;

final class SlugGeneratorByCipher implements SlugGeneratorInterface
{
    public function generate(string $value): string
    {
        $slugger = new AsciiSlugger();

        $value = preg_replace('/\..*/', '', $value);

        $slug = $slugger->slug($value)->lower()->toString();

        if ('' === $slug) {
            throw new DomainException('Cannot generate slug from the given cipher.');
        }

        return $slug;
    }
}
