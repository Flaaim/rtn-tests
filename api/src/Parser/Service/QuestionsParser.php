<?php

declare(strict_types=1);

namespace App\Parser\Service;

use App\Parser\Entity\Parser\HostMapper;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class QuestionsParser
{
    public function __construct(
        private readonly HttpClientInterface $client
    ) {}

    public function fetch(string $host, string $cookie, string $branchId, string $ticketId): array
    {
        try{
            $response = $this->client->request('POST', $host .'/'. ltrim(HostMapper::PATH_QUESTIONS->value), [
                'headers' => [
                    'Cookie' => $cookie,
                ],
                'body' => [
                    'branchId' => $branchId,
                    'ticketId' => $ticketId,
                ]
            ]);
            return $response->toArray();
        }catch (\Throwable $e){
            throw $e;
        }
    }
}
