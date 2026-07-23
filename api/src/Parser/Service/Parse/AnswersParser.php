<?php

declare(strict_types=1);

namespace App\Parser\Service\Parse;

use App\Parser\Entity\Parser\HostMapper;
use App\Parser\Exception\RemoteException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

final class AnswersParser
{
    public function __construct(
        private HttpClientInterface $client
    ) {}

    public function parse(
        array $questions,
        string $cookie,
        string $materialId,
        string $host
    ): array {
        try {
            foreach ($questions as $question) {
                $question['answers'] = $this->client->request('POST', $host . '/' . ltrim(HostMapper::PATH_ANSWERS->value), [
                    'headers' => [
                        'Cookie' => $cookie,
                    ],
                    'body' => [
                        'materialId' => $materialId,
                        'questionId' => $question['Id'],
                    ],
                ]);

                $question['answers'] = $this->matchString($question['answers']->getContent());
            }
            return $questions;
        } catch (Throwable $e) {
            throw new RemoteException($e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    private function matchString(string $content): array
    {
        if (preg_match('#"rows"\s*:\s*(.+)\s*}\,#', $content, $matches)) {
            $rowsTable = $matches[1];

            return json_decode($rowsTable, true) ?? [];
        }
    }
}
