<?php

declare(strict_types=1);

namespace App\Testing\Query\Attempt;

use App\Testing\Entity\Attempt\QuestionForm;
use App\Testing\Entity\Attempt\Status;
use Doctrine\DBAL\Connection;

/** @psalm-suppress UnusedClass */
final class AttemptFetcher implements AttemptFetcherInterface
{
    public function __construct(
        private readonly Connection $connection
    ) {}

    public function getOneById(string $attemptId): array
    {
        $qb = $this->connection->createQueryBuilder();

        $attempt = $qb->select('a.id, a.status, a.ticket_number, a.questions_snapshot')
            ->from('attempts', 'a')
            ->where($qb->expr()->eq('a.id', ':id'))
            ->setParameter('id', $attemptId)
            ->executeQuery()
            ->fetchAssociative();

        if (false === $attempt || empty($attempt['questions_snapshot'])) {
            return [];
        }

        $questionsSnapshot = json_decode($attempt['questions_snapshot'], true, 512, JSON_THROW_ON_ERROR);

        if (empty($questionsSnapshot)) {
            return [];
        }

        foreach ($questionsSnapshot as &$question) {
            if (isset($question['form']) && QuestionForm::MATCHING->value === $question['form']) {
                shuffle($question['answers']['right']);
                continue;
            }

            $safeAnswers = array_map(static function (array $answer) {
                unset($answer['isCorrect']);
                return $answer;
            }, $question['answers']);

            shuffle($safeAnswers);
            $question['answers'] = $safeAnswers;
        }
        unset($question);

        return [
            'id' => $attempt['id'],
            'status' => $attempt['status'],
            'ticket_number' => $attempt['ticket_number'],
            'questions_snapshot' => $questionsSnapshot,
        ];
    }

    public function getAttemptResult(string $attemptId, string $userId): array
    {
        $qb = $this->connection->createQueryBuilder();

        $attempt = $qb->select('a.id, a.status, a.questions_snapshot, a.score, a.mistakes, a.started_at, a.finished_at, a.ticket_number, a.test_id, t.name, t.cipher, t.allowed_mistakes, u.email')
            ->from('attempts', 'a')
            ->leftJoin('a', 'tests', 't', 'a.test_id = t.id')
            ->leftJoin('a', 'users', 'u', 'a.user_id = u.id')
            ->andWhere($qb->expr()->eq('a.id', ':id'))
            ->andWhere($qb->expr()->eq('u.id', ':userId'))
            ->setParameter('id', $attemptId)
            ->setParameter('userId', $userId)
            ->executeQuery()
            ->fetchAssociative();

        if (false === $attempt || empty($attempt['questions_snapshot'])) {
            return [];
        }
        $questionsSnapshot = json_decode($attempt['questions_snapshot'], true, 512, JSON_THROW_ON_ERROR);

        if (empty($questionsSnapshot)) {
            return [];
        }

        $answersQb = $this->connection->createQueryBuilder();
        $answersRows = $answersQb->select('question_id, selected_answers_ids, is_correct')
            ->from('answers')
            ->where($answersQb->expr()->eq('attempt_id', ':attemptId'))
            ->setParameter('attemptId', $attemptId)
            ->executeQuery()
            ->fetchAllAssociative();

        $userAnswersByQuestionId = [];
        foreach ($answersRows as $ans) {
            $userAnswersByQuestionId[$ans['question_id']] = [
                'selected_ids' => json_decode($ans['selected_answers_ids'], true, 512, JSON_THROW_ON_ERROR),
                'is_correct' => (bool)$ans['is_correct'],
            ];
        }

        foreach ($questionsSnapshot as &$question) {
            $qId = $question['id'];
            $question['user_result'] = $userAnswersByQuestionId[$qId] ?? null;
        }

        unset($question);

        return [
            'id' => $attempt['id'],
            'status' => $attempt['status'],
            'score' => $attempt['score'],
            'mistakes' => $attempt['mistakes'],
            'started_at' => $attempt['started_at'],
            'finished_at' => $attempt['finished_at'],
            'ticket_number' => $attempt['ticket_number'],
            'email' => $attempt['email'],
            'test' => [
                'test_id' => $attempt['test_id'],
                'name' => $attempt['name'],
                'cipher' => $attempt['cipher'],
                'allowed_mistakes' => $attempt['allowed_mistakes'],
            ],
            'questions_snapshot' => $questionsSnapshot,
        ];
    }

    public function getByUser(string $userId, int $page, int $limit): array
    {
        $page = max(1, $page);
        $limit = min(max(1, $limit), 100);
        $offset = ($page - 1) * $limit;

        $qb = $this->connection->createQueryBuilder();

        $rows = $qb->select('a.id, a.status, a.score, a.mistakes, a.started_at, a.finished_at, a.ticket_number, t.name, t.cipher, t.allowed_mistakes')
            ->from('attempts', 'a')
            ->leftJoin('a', 'tests', 't', 'a.test_id = t.id')
            ->where($qb->expr()->eq('a.user_id', ':userId'))
            ->andWhere($qb->expr()->neq('a.status', ':status'))
            ->setParameter('userId', $userId)
            ->setParameter('status', Status::STATUS_IN_PROGRESS)
            ->orderBy('a.started_at', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->executeQuery()
            ->fetchAllAssociative();

        $countQb = $this->connection->createQueryBuilder();
        $totalCount = (int)$countQb->select('COUNT(a.id)')
            ->from('attempts', 'a')
            ->andWhere($countQb->expr()->eq('a.user_id', ':userId'))
            ->andWhere($countQb->expr()->neq('a.status', ':status'))
            ->setParameter('userId', $userId)
            ->setParameter('status', Status::STATUS_IN_PROGRESS)
            ->executeQuery()
            ->fetchOne();

        return [
            'items' => $rows,
            'totalCount' => $totalCount,
        ];
    }

    public function getUserStats(string $userId): array
    {
        $qb = $this->connection->createQueryBuilder();

        $completedTests = $qb->select('COUNT(DISTINCT a.id)')
            ->from('attempts', 'a')
            ->where($qb->expr()->eq('a.user_id', ':userId'))
            ->andWhere($qb->expr()->eq('a.status', ':status'))
            ->setParameter('userId', $userId)
            ->setParameter('status', Status::STATUS_PASSED)
            ->executeQuery()
            ->fetchOne();

        if (false === $completedTests) {
            $completedTests = 0;
        }

        $inProgressTests = $qb->select('COUNT(DISTINCT a.id)')
            ->from('attempts', 'a')
            ->where($qb->expr()->eq('a.user_id', ':userId'))
            ->andWhere($qb->expr()->eq('a.status', ':status'))
            ->setParameter('userId', $userId)
            ->setParameter('status', Status::STATUS_IN_PROGRESS)
            ->executeQuery()
            ->fetchOne();

        if (false === $inProgressTests) {
            $inProgressTests = 0;
        }

        $result = $qb->select('SUM(a.score) as score, SUM(a.mistakes) as mistakes')
            ->from('attempts', 'a')
            ->where($qb->expr()->eq('a.user_id', ':userId'))
            ->andWhere($qb->expr()->neq('a.status', ':status'))
            ->setParameter('userId', $userId)
            ->setParameter('status', Status::STATUS_IN_PROGRESS)
            ->executeQuery()
            ->fetchAssociative();

        $data = [
            'score' => (int)($result['score'] ?? 0),
            'mistakes' => (int)($result['mistakes'] ?? 0),
        ];

        $count = $data['score'] + $data['mistakes'];

        $averageScore = ($count > 0) ? (($data['score'] * 100) / $count) : 0;

        return [
            'completedTests' => $completedTests,
            'inProgressTests' => $inProgressTests,
            'averageScore' => $averageScore,
        ];
    }
}
