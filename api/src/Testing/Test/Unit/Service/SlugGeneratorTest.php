<?php

declare(strict_types=1);

namespace App\Testing\Test\Unit\Service;

use App\Testing\Service\SlugGeneratorByCipher;
use DomainException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class SlugGeneratorTest extends TestCase
{
    public function testSlugGenerator(): void
    {
        $generator = new SlugGeneratorByCipher();
        $slug = $generator->generate('ОТ 201.18');

        self::assertEquals('ot-201', $slug);
    }

    #[DataProvider('provideDifferentSlugsCases')]
    public function testDifferentSlugs(string $slug, string $expected): void
    {
        $generator = new SlugGeneratorByCipher();
        self::assertEquals($expected, $generator->generate($slug));
    }

    public static function provideDifferentSlugsCases(): iterable
    {
        return [
            ['ОТ 201.18', 'ot-201'],
            ['ОТ 201..18', 'ot-201'],
            ['ОТ-201.18-', 'ot-201'],
        ];
    }

    public function testEmpty(): void
    {
        $generator = new SlugGeneratorByCipher();

        self::expectException(DomainException::class);
        self::expectExceptionMessage('Cannot generate slug from the given cipher.');
        $generator->generate('');
    }
}
