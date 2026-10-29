<?php

namespace App\Course\Service\FileRenderer;

interface FileRendererInterface
{
    public function render(string $courseId): void;
}
