<?php

declare(strict_types=1);

namespace App\Parser\Test\Unit\Service;

use App\Parser\Entity\Parser\HostMapper;
use App\Parser\Service\QuestionsParser;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;


final class QuestionsParserTest extends TestCase
{
    protected function setUp(): void
    {
        $this->client = $this->createMock(HttpClientInterface::class);
    }
    public function testSuccess(): void
    {
        $host = 'http://example.com';
        $cookie = 'some_cookie';
        $branchId = 'branchId';
        $ticketId = 'ticketId';

        $parser = new QuestionsParser($this->client);

        $this->client->expects(self::once())->method('request')->with(
            self::equalTo('POST'),
            self::equalTo($host. '/' . HostMapper::PATH_QUESTIONS->value),
            self::equalTo([
                'headers' => [
                    'Cookie' => $cookie,
                ],
                'body' => [
                    'branchId' => $branchId,
                    'ticketId' => $ticketId,
                ]
            ])
        )->willReturn($this->createStub(ResponseInterface::class));

        $parser->fetch($host, $cookie, $branchId, $ticketId);
    }

    public function testFailed(): void
    {
        $host = 'http://example.com';
        $cookie = 'some_cookie';
        $branchId = 'branchId';
        $ticketId = 'ticketId';
        $parser = new QuestionsParser($this->client);

        $this->client->expects(self::once())->method('request')->willThrowException(new \Exception());

        self::expectException(\Exception::class);
        $parser->fetch($host, $cookie, $branchId, $ticketId);
    }
}
