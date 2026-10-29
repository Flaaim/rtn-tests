<?php

declare(strict_types=1);

namespace App\SharedDomain\Test\Unit\Filesystem;

use App\SharedDomain\Filesystem\QuestionImages\QuestionImagesFilePath;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class FileSystemPathTest extends TestCase
{
    public function testSuccess(): void
    {
        $file = new QuestionImagesFilePath(sys_get_temp_dir());
        self::assertEquals(sys_get_temp_dir(), $file->getValue());
    }

    public function testEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new QuestionImagesFilePath('');
    }

    public function testTrimSlash(): void
    {
        $file = new QuestionImagesFilePath(sys_get_temp_dir() . '/');
        self::assertEquals(sys_get_temp_dir(), $file->getValue());
    }
}
