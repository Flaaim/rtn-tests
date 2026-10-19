<?php

declare(strict_types=1);

namespace App\Testing\Query\Attempt;

interface AttemptFetcherInterface
{
    public function getOneById(string $attemptId): array;

    public function getAttemptResult(string $attemptId, string $userId): array;

    public function getByUser(string $userId, int $page, int $limit): array;

    public function getUserStats(string $userId): array;

    public function findFailedQuestionsIds(string $userId, string $testId): array;

    public function getPopularTests(string $userId): array;
}
