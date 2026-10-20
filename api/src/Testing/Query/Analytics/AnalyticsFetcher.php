<?php

declare(strict_types=1);

namespace App\Testing\Query\Analytics;

use App\Testing\Entity\Attempt\Status;
use Doctrine\DBAL\Connection;

/** @psalm-suppress UnusedClass */
final readonly class AnalyticsFetcher implements AnalyticsFetcherInterface
{
    public function __construct(
        private Connection $connection
    ) {}

    public function getPopularTests(string $userId): array
    {
        $qb = $this->connection->createQueryBuilder();

        $popularTests = $qb->select(
            't.id',
            't.name',
            't.cipher',
            'COUNT(a.test_id) as tests_attempts',
            'SUM(CASE WHEN a.status != :statusInProgress THEN a.score ELSE 0 END) as test_score',
            'SUM(CASE WHEN a.status != :statusInProgress THEN a.mistakes ELSE 0 END) as test_mistakes'
        )
            ->from('attempts', 'a')
            ->join('a', 'tests', 't', 'a.test_id = t.id')
            ->where('a.user_id = :userId')
            ->groupBy('t.id', 't.name', 't.cipher')
            ->orderBy('tests_attempts', 'DESC')
            ->setMaxResults(10)
            ->setParameter('userId', $userId)
            ->setParameter('statusInProgress', Status::STATUS_IN_PROGRESS)
            ->executeQuery()
            ->fetchAllAssociative();

        $popularTestsData = [];

        foreach ($popularTests as $test) {
            $tScore = (int)($test['test_score'] ?? 0);
            $tMistakes = (int)($test['test_mistakes'] ?? 0);
            $tCount = $tScore + $tMistakes;

            $testAverage = ($tCount > 0) ? (($tScore * 100) / $tCount) : 0;

            $popularTestsData[] = [
                'id' => $test['id'],
                'name' => $test['name'],
                'cipher' => $test['cipher'],
                'testsAttempts' => $test['tests_attempts'],
                'averageScore'  => round($testAverage, 2),
            ];
        }

        return $popularTestsData;
    }
}
