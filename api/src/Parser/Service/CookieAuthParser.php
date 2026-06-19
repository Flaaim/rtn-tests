<?php

declare(strict_types=1);

namespace App\Parser\Service;

use App\Parser\Entity\Parser\Host;
use App\Parser\Entity\Parser\HostMapper;
use App\Parser\Exception\RemoteException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

final readonly class CookieAuthParser
{
    public function __construct(
        private ClientInterface $client
    ) {
    }

    public function fetch(Host $host, string $login, string $password): ResponseInterface
    {
        try{
            return $this->client->request('POST', $host->getValue() . '/' . ltrim(HostMapper::AUTH->value), [
                'form_params' => [
                    'login' => $login,
                    'password' => $password,
                ]
            ]);
        }catch (GuzzleException $e){
            throw new RemoteException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
