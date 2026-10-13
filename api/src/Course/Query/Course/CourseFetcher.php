<?php

declare(strict_types=1);

namespace App\Course\Query\Course;

use App\Course\Entity\Course\QuestionForm;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

/** @psalm-suppress UnusedClass */
final readonly class CourseFetcher implements CourseFetcherInterface
{
    public function __construct(
        private Connection $connection
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

    public function getQuestionIdsByCourseId(string $courseId): array
    {
        $qb = $this->connection->createQueryBuilder();

        $result = $qb->select('q.id')
            ->from('questions', 'q')
            ->where($qb->expr()->eq('q.course_id', ':courseId'))
            ->setParameter('courseId', $courseId)
            ->executeQuery();

        $data = [];
        while ($rows = $result->fetchAssociative()) {
            $data[] = $rows['id'];
        }
        return $data;
    }

    public function getLookupList(): array
    {
        $qb = $this->connection->createQueryBuilder();

        $result = $qb->select('c.course_id, c.name')
            ->from('courses', 'c')
            ->where($qb->expr()->eq('c.status', ':status'))
            ->setParameter('status', 'created')
            ->executeQuery();

        return $result->fetchAllAssociative();
    }

    public function getQuestionsByCourseIds(array $courseIds): array
    {
        $qb = $this->connection->createQueryBuilder();

        $result = $qb->select('q.id, q.text, q.question_img, q.answers, q.form')
            ->from('questions', 'q')
            ->where($qb->expr()->in('q.course_id', ':courseId'))
            ->setParameter('courseId', $courseIds, ArrayParameterType::STRING)
            ->executeQuery();

        $rows = $result->fetchAllAssociative();

        if (empty($rows)) {
            return [];
        }

        $questions = [];

        foreach ($rows as $row) {
            $questions[] = [
                'id'          => $row['id'],
                'text'        => $row['text'],
                'question_img' => $row['question_img'],
                'form'        => $row['form'],
                'answers'     => json_decode($row['answers'], true, 512, JSON_THROW_ON_ERROR),
            ];
        }

        return $questions;
    }

    public function getQuestions(array $questionIds): array
    {
        $qb = $this->connection->createQueryBuilder();
        $result = $qb->select('q.id, q.text, q.question_img, q.answers, q.form')
            ->from('questions', 'q')
            ->where($qb->expr()->in('q.id', ':questionIds'))
            ->setParameter('questionIds', $questionIds, ArrayParameterType::STRING)
            ->executeQuery();

        $questions = $result->fetchAllAssociative();

        if (empty($questions)) {
            return [];
        }
        $data = [];
        foreach ($questions as $question) {
            $answers = json_decode($question['answers'], true, 512, JSON_THROW_ON_ERROR);

            if (QuestionForm::MATCHING === $question['form']) {
                $leftColumn = [];
                $rightColumn = [];

                foreach ($answers as $answer) {
                    $text = (string)($answer['text'] ?? '');

                    $answerParts = explode('|||', $text, 2);

                    if (\count($answerParts) < 2) {
                        continue;
                    }

                    $leftColumn[] = [
                        'id' => $answer['id'],
                        'text' => trim($answerParts[0]),
                        'image' => $answer['answerImg'] ?? null,
                    ];

                    $rightColumn[] = [
                        'id' => $answer['id'],
                        'text' => trim($answerParts[1]),
                    ];
                }

                $finalAnswers = [
                    'left' => $leftColumn,
                    'right' => $rightColumn,
                ];
            } elseif (QuestionForm::SEQUENCE === $question['form']) {
                $finalAnswers = $answers;
            } else {
                $finalAnswers = $answers;
            }

            $data[] = [
                'id' => $question['id'],
                'text' => $question['text'],
                'question_img' => $question['question_img'],
                'answers' => $finalAnswers,
                'form' => $question['form'],
            ];
        }

        return $data;
    }

    public function getQuestion(string $questionId): array
    {
        $qb = $this->connection->createQueryBuilder();

        $result = $qb->select('
        q.id,
        q.text,
        q.answers,
        q.form,
        t.id as test_id,
        t.name as test_name,
        t.description as test_description,
        t.slug,
        c.id as category_id,
        c.name as category_name,
        c.slug as category_slug,
        p.id as parent_id,
        p.name as parent_name,
        p.slug as parent_slug
        ')
            ->from('questions', 'q')
            ->join('q', 'tests', 't', 't.course_ids @> jsonb_build_array(q.course_id::text)')
            ->join('t', 'test_categories', 'c', 't.category_id = c.id')
            ->leftJoin('c', 'test_categories', 'p', 'c.parent_id = p.id')
            ->where($qb->expr()->eq('q.id', ':questionId'))
            ->setParameter('questionId', $questionId)
            ->executeQuery()
            ->fetchAssociative();

        if (false === $result) {
            return [];
        }

        $answers = json_decode($result['answers'], true, 512, JSON_THROW_ON_ERROR);

        return [
            'id' => $result['id'],
            'text' => $result['text'],
            'answers' => $answers,
            'form' => $result['form'],
            'test' => [
                'id' => $result['test_id'],
                'name' => $result['test_name'],
                'description' => $result['test_description'],
                'slug' => $result['slug'],
            ],
            'category' => [
                'name' => $result['category_name'],
                'slug' => $result['category_slug'],
                'parent' => $result['parent_id'] ? [
                    'id' => $result['parent_id'],
                    'name' => $result['parent_name'],
                    'slug' => $result['parent_slug'],
                ] : null,
            ],
        ];
    }
}
