<?php

declare(strict_types=1);

namespace App\Testing\Query\Test;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

/** @psalm-suppress UnusedClass */
final class TestFetcher implements TestFetcherInterface
{
    public function __construct(
        private readonly Connection $connection
    ) {}

    /**
     * @return array{items: list<array<string, mixed>>, totalCount: int}
     * @throws Exception
     */
    public function getPaginated(int $page = 1, int $limit = 15, ?string $search = null): array
    {
        $page = max(1, $page);
        $limit = min(max(1, $limit), 100);
        $offset = ($page - 1) * $limit;

        $qb = $this->connection->createQueryBuilder();

        $qb->select('t.id, t.status, t.name, t.created_at, t.cipher')
            ->from('tests', 't');

        $normalizedSearch = null !== $search ? trim($search) : '';

        if (null !== $search && '' !== trim($search)) {
            $qb->andWhere($qb->expr()->or(
                $qb->expr()->like('t.name', ':search'),
                $qb->expr()->like('t.cipher', ':search')
            ))
                ->setParameter('search', '%' . $normalizedSearch . '%');
        }

        $countQb = clone $qb;
        $totalCount = (int)$countQb->select('COUNT(t.id)')
            ->executeQuery()
            ->fetchOne();

        $rows = $qb->select('t.id, t.status, t.name, t.created_at, t.cipher')
            ->orderBy('t.name', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->executeQuery()
            ->fetchAllAssociative();

        return [
            'items' => $rows,
            'totalCount' => $totalCount,
        ];
    }

    public function getOneById(string $id): array
    {
        $qb = $this->connection->createQueryBuilder();

        $result = $qb->select(
            '
            t.id,
            t.name,
            t.cipher,
            t.description,
            t.status,
            t.allowed_mistakes,
            t.course_ids,
            t.slug,
            t.tickets,
            t.created_at'
        )->from('tests', 't')

            ->where($qb->expr()->eq('t.id', ':id'))
            ->setParameter('id', $id)
            ->executeQuery();

        $row = $result->fetchAssociative();

        if (false !== $row) {
            $courseIds = json_decode($row['course_ids'], true, JSON_THROW_ON_ERROR);

            if (!empty($courseIds) && \is_array($courseIds)) {
                $qbCourses = $this->connection->createQueryBuilder();

                $courses = $qb->select('c.course_id, c.name')
                    ->from('courses', 'c')
                    ->where($qbCourses->expr()->in('c.course_id', ':courseIds'))
                    ->setParameter('courseIds', $courseIds, ArrayParameterType::STRING)
                    ->executeQuery()
                    ->fetchAllAssociative();

                $row['courses'] = $courses;
            } else {
                $row['courses'] = [];
            }

            $tickets = json_decode($row['tickets'], true, JSON_THROW_ON_ERROR);
            $allQuestionIds = [];
            foreach ($tickets as $ticket) {
                if (!empty($ticket['questionIds']) && \is_array($ticket['questionIds'])) {
                    $allQuestionIds = array_merge($allQuestionIds, $ticket['questionIds']);
                }
            }
            $allQuestionIds = array_unique($allQuestionIds);

            $questionsById = [];

            if (!empty($allQuestionIds)) {
                $qbQuestions = $this->connection->createQueryBuilder();
                $questionsRaw = $qbQuestions->select('q.id, q.text, q.question_img, q.form, q.answers')
                    ->from('questions', 'q')
                    ->where($qbQuestions->expr()->in('q.id', ':questionIds'))
                    ->setParameter('questionIds', $allQuestionIds, ArrayParameterType::STRING)
                    ->executeQuery()
                    ->fetchAllAssociative();

                foreach ($questionsRaw as $q) {
                    $q['answers'] = json_decode($q['answers'], true, 512, JSON_THROW_ON_ERROR);
                    $questionsById[$q['id']] = $q;
                }
            }
            $data = [];
            foreach ($tickets as $ticket) {
                $ticketQuestions = [];
                if (!empty($ticket['questionIds']) && \is_array($ticket['questionIds'])) {
                    foreach ($ticket['questionIds'] as $qId) {
                        if (isset($questionsById[$qId])) {
                            $ticketQuestions[] = $questionsById[$qId];
                        }
                    }
                }

                $data[] = [
                    'number' => $ticket['number'],
                    'questions' => $ticketQuestions,
                ];
            }
            $row['tickets'] = $data;
        }

        return $row ?: [];
    }
}
