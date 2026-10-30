<?php

declare(strict_types=1);

namespace App\Course\Service\FileRenderer;

interface DirectoryPreparerInterface
{
    public function prepare(string $courseId): string;
}
