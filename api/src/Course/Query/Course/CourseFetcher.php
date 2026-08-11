<?php

declare(strict_types=1);

namespace App\Course\Query\Course;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

/** @psalm-suppress UnusedClass */
final class CourseFetcher implements CourseFetcherInterface
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

        $qb->select('c.course_id, c.status, c.name, c.created_at, c.cipher')
            ->from('courses', 'c');

        $normalizedSearch = null !== $search ? trim($search) : '';

        if (null !== $search && '' !== trim($search)) {
            $qb->andWhere($qb->expr()->or(
                $qb->expr()->like('c.name', ':search'),
                $qb->expr()->like('c.cipher', ':search')
            ))
                ->setParameter('search', '%' . $normalizedSearch . '%');
        }

        $countQb = clone $qb;
        $totalCount = (int)$countQb->select('COUNT(c.course_id)')
            ->executeQuery()
            ->fetchOne();

        $rows = $qb->select('c.course_id, c.status, c.name, c.created_at, c.cipher')
            ->orderBy('c.name', 'ASC')
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

        $result = $qb->select('c.course_id, c.status, c.name, c.created_at, c.cipher, q.id as question_id, q.text, q.question_img, q.answers, q.form')
            ->from('courses', 'c')
            ->leftJoin('c', 'questions', 'q', 'c.course_id = q.course_id')
            ->where($qb->expr()->eq('c.course_id', ':id'))
            ->setParameter('id', $id)
            ->executeQuery();

        $rows = $result->fetchAllAssociative();

        if (empty($rows)) {
            return [];
        }

        $data = [
            'course_id'  => $rows[0]['course_id'],
            'name'       => $rows[0]['name'],
            'status'     => $rows[0]['status'],
            'created_at' => $rows[0]['created_at'],
            'cipher'     => $rows[0]['cipher'],
            'questions'  => [],
        ];

        foreach ($rows as $row) {
            if (null !== $row['question_id']) {
                $data['questions'][] = [
                    'id'          => $row['question_id'],
                    'text'        => $row['text'],
                    'question_img' => $row['question_img'],
                    'answers'     => json_decode($row['answers'], true, 512, JSON_THROW_ON_ERROR),
                    'form'        => $row['form'],
                ];
            }
        }

        return $data;
    }
}
