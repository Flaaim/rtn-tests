<?php

declare(strict_types=1);

namespace App\Testing\Entity;

interface CourseRepository
{
    public function add(Course $course): void;
}
