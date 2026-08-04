<?php

declare(strict_types=1);

namespace App\Testing\Test\Unit\Service\Downloader;

use App\Testing\Service\Downloader\FilenameGenerator;
use PHPUnit\Framework\TestCase;

final class FilenameGeneratorTest extends TestCase
{
    public function testSuccess(): void
    {
        $filenameGenerator = new FilenameGenerator();

        $filename = $filenameGenerator->generateFilename('https://olimpoks.hydroschool.ru/QuestionImages/c92192/145d3d30-5398-478d-bbb9-82c820f8ac1f/9/1.jpg');
        $pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}\.jpg$/i';

        self::assertMatchesRegularExpression($pattern, $filename);
    }

    public function testEmpty(): void
    {
        $filenameGenerator = new FilenameGenerator();
        self::expectException(\InvalidArgumentException::class);
        $filenameGenerator->generateFilename('');
    }
}
