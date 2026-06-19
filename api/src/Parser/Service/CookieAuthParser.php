<?php

declare(strict_types=1);

namespace App\Parser\Service;

use App\Parser\Entity\Parser\Host;
use App\Parser\Entity\Parser\HostMapper;
use App\Parser\Exception\RemoteException;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class CookieAuthParser
{
    public function __construct(
        private HttpClientInterface $client
    ) {}

    public function fetch(Host $host, string $login, string $password): array
    {
        try {
            $response = $this->client->request('POST', $host->getValue() . '/' . ltrim(HostMapper::AUTH->value), [
                'body' => [
                    'login' => $login,
                    'password' => $password,
                ],
            ]);
            return $response->getHeaders()['set-cookie'] ?? [];
        } catch (TransportExceptionInterface $e) {
            throw new RemoteException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
