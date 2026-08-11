<?php

declare(strict_types=1);

namespace App\Testing\Entity\Course;

final class TrapPhrases
{
    private const array SEQUENCE_PHRASES = [
        'установите последовательность',
        'установите правильную последовательность',
        'порядок действий',
    ];

    private const array MATCHING_PHRASES = [
        'установите соответствие',
    ];

    public static function isSequence(string $text): bool
    {
        return self::containsAny($text, self::SEQUENCE_PHRASES);
    }

    public static function isMatching(string $text): bool
    {
        return self::containsAny($text, self::MATCHING_PHRASES);
    }

    private static function containsAny(string $text, array $phrases): bool
    {
        $lowerText = mb_strtolower($text);
        return array_any($phrases, static fn ($phrase) => str_contains($lowerText, $phrase));
    }
}
