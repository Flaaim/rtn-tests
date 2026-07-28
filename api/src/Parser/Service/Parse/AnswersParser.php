<?php

declare(strict_types=1);

namespace App\Parser\Service\Parse;

use App\Parser\Entity\Parser\DTO\AnswerDTO;
use App\Parser\Entity\Parser\DTO\QuestionDTO;
use App\Parser\Entity\Parser\HostMapper;
use App\Parser\Exception\RemoteException;
use App\Parser\Service\SanitizerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

final class AnswersParser
{
    /** @psalm-suppress PossiblyUnusedMethod  */
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly SanitizerInterface $sanitizer,
    ) {}

    public function fetch(
        array $questions,
        string $cookie,
        string $materialId,
        string $host
    ): array {
        try {
            foreach ($questions as $question) {
                /** @var QuestionDTO $question */
                $response = $this->client->request('POST', $host . '/' . ltrim(HostMapper::PATH_ANSWERS->value), [
                    'headers' => [
                        'Cookie' => $cookie,
                    ],
                    'body' => [
                        'materialId' => $materialId,
                        'questionId' => $question->id,
                    ],
                ]);
                $question->answers = $this->extractData($response->getContent(), $host);
            }
            return $questions;
        } catch (Throwable $e) {
            throw new RemoteException($e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    private function extractData(string $content, string $host): array
    {
        if (preg_match('#"rows"\s*:\s*(.+)\s*}\,#', $content, $matches)) {
            $rowsTable = $matches[1];

            $rows = json_decode($rowsTable, true) ?? null;
            if (null === $rows) {
                throw new RemoteException('Cannot decode rows data: ' . $rowsTable);
            }

            return array_map(function (array $row) use ($host) {
                $row['answerImg'] = $this->sanitizer->extractImgFromAnswerText($row['Text'], $host);
                $row['Text'] = $this->sanitizer->cleanTextContent($row['Text']);
                return AnswerDTO::fromArray($row);
            }, $rows);
        }

        throw new RemoteException('Can not parse answers: ' . $content);
    }
}
