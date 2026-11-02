<?php

declare(strict_types=1);

namespace App\CourseAnswers\MessageHandlers\Course;

use App\Course\Event\CourseRendered;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CourseRenderedHandler
{
    public function __invoke(CourseRendered $message): void {}
}
