<?php

declare(strict_types=1);

namespace App\Parser\Test\Unit\Service;

use App\Parser\Entity\Parser\Host;
use App\Parser\Entity\Parser\HostMapper;
use App\Parser\Exception\RemoteException;
use App\Parser\Service\Parse\CookieAuthParser;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @internal
 * @coversNothing
 */
final class CookieAuthParserTest extends TestCase
{
    private HttpClientInterface&MockObject $client;

    protected function setUp(): void
    {
        $this->client = $this->createMock(HttpClientInterface::class);
    }

    public function testSuccess(): void
    {
        $host = new Host('https://example.com');
        $login = 'login';
        $password = 'password';
        $parser = new CookieAuthParser($this->client);

        $this->client->expects(self::once())->method('request')->with(
            self::equalTo('POST'),
            self::equalTo($host->getValue() . \DIRECTORY_SEPARATOR . HostMapper::AUTH->value),
            self::equalTo([
                'body' => [
                    'login' => $login,
                    'password' => $password,
                ],
            ])
        )->willReturn(self::createStub(ResponseInterface::class));

        $parser->fetch($host, $login, $password);
    }

    public function testFailed(): void
    {
        $host = new Host('https://example.com');
        $parser = new CookieAuthParser($this->client);
        $this->client->expects(self::once())->method('request')->willThrowException(new TransportException('Network timeout'));

        $this->expectException(RemoteException::class);
        $this->expectExceptionMessage('Network timeout');
        $parser->fetch($host, 'login', 'password');
    }
}
