<?php

declare(strict_types=1);

namespace App\Testing\Service;

use DomainException;
use RuntimeException;
use Transliterator;

final class SlugGeneratorByCipher implements SlugGeneratorInterface
{
    public function generate(string $value): string
    {
        $transliterator = Transliterator::create('Any-Latin; Latin-ASCII');

        if (null === $transliterator) {
            throw new RuntimeException('Transliterator extension is not properly configured.');
        }
        $transliterated = $transliterator->transliterate($value);
        if (false === $transliterated) {
            throw new DomainException('Transliteration failed.');
        }
        $value = mb_strtolower($transliterated);

        $value = preg_replace('/\..*/', '', $value);

        $value = preg_replace('/[^a-zA-Z0-9]+/', '', $value);

        if (null === $value) {
            throw new RuntimeException('Transliteration value is null.');
        }
        $value = trim($value, '-');

        if ('' === $value) {
            throw new DomainException('Cannot generate slug from the given cipher.');
        }

        return $value;
    }
}
