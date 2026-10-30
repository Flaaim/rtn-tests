<?php

declare(strict_types=1);

namespace App\Course\Service\FileRenderer;

interface FileRendererInterface
{
    public function render(array $questions, string $courseId): string;
}
