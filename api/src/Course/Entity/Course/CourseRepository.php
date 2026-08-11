<?php

declare(strict_types=1);

namespace App\Course\Entity\Course;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use DomainException;

final class CourseRepository
{
    private readonly EntityRepository $repo;

    public function __construct(
        private readonly EntityManagerInterface $em
    ) {
        $this->repo = $em->getRepository(Course::class);
    }

    public function add(Course $course): void
    {
        $this->em->persist($course);
    }

    public function get(CourseId $id): Course
    {
        $course = $this->repo->find($id);
        if (null === $course) {
            throw new DomainException('Course not found.');
        }
        /** @var Course $course */
        return $course;
    }

    public function remove(Course $course): void
    {
        $this->em->remove($course);
    }
}
