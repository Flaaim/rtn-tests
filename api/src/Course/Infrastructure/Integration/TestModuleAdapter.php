<?php

declare(strict_types=1);

namespace App\Course\Infrastructure\Integration;

use App\Course\Interfaces\TestInterface;
use App\Testing\Api\TestApi;

final readonly class TestModuleAdapter implements TestInterface
{
    public function __construct(
        private TestApi $testApi,
    ) {}

    public function getTestByCourseId(string $courseId): ?array
    {
        $test = $this->testApi->getTestByCourseId($courseId);

        if (null === $test) {
            return null;
        }

        return [
            'id' => $test->testId,
            'name' => $test->name,
            'description' => $test->description,
            'slug' => $test->slug,
            'category' => [
                'name' => $test->category->name,
                'slug' => $test->category->slug,
                'parent' => $test->category->parent,
            ],
        ];
    }
}
