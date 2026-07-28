<?php

declare(strict_types=1);

namespace App\Parser\Service\Parse;

use App\Parser\Entity\Parser\DTO\QuestionDTO;
use App\Parser\Entity\Parser\HostMapper;
use App\Parser\Exception\RemoteException;
use App\Parser\Service\SanitizerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

final class QuestionsParser
{
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly SanitizerInterface $sanitizer,
    ) {}

    public function fetch(string $host, string $cookie, string $branchId, string $ticketId): array
    {
        try {
            $response = $this->client->request('POST', $host . '/' . ltrim(HostMapper::PATH_QUESTIONS->value), [
                'headers' => [
                    'Cookie' => $cookie,
                ],
                'body' => [
                    'branchId' => $branchId,
                    'ticketId' => $ticketId,
                ],
            ]);
            return $this->extractData($response->toArray(), $host);
        } catch (Throwable $e) {
            throw new RemoteException($e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    private function extractData(array $response, string $host): array
    {
        if (isset($response['rows'])) {
            return array_map(function (array $row) use ($host) {
                $row['Text'] = $this->sanitizer->cleanTextContent($row['Text']);
                $row['QuestionMainImg'] = $this->sanitizer->extractImgFromQuestionMainImg($row['QuestionMainImg'], $host);
                return QuestionDTO::fromArray($row);
            }, $response['rows']);
        }
        throw new RemoteException('Can not extract rows from response');
    }
}
