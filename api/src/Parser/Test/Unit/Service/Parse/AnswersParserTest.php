<?php

declare(strict_types=1);

namespace App\Parser\Test\Unit\Service\Parse;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @internal
 * @coversNothing
 */
final class AnswersParserTest extends TestCase
{
    private HttpClientInterface&MockObject $client;

    protected function setUp(): void
    {
        $this->client = $this->createMock(HttpClientInterface::class);
    }

    public function testSuccess(): void {}

    private function questionDataProvider(): array
    {
        return [
            '',
        ];
    }
}
