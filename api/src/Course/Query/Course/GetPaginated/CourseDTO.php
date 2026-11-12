<?php

declare(strict_types=1);

namespace App\Course\Query\Course\GetPaginated;

use DateTimeImmutable;

final readonly class CourseDTO
{
    public function __construct(
        public string $courseId,
        public string $name,
        public string $status,
        public string $createdAt,
        public string $cipher,
        public ?ProductDTO $product = null,
    ) {}

    public static function fromArray(array $data): self
    {
        $product = null;
        if (!empty($data['product'])) {
            $product = ProductDTO::fromArray($data['product']);
        }

        return new self(
            courseId: $data['course_id'],
            name: $data['name'],
            status: $data['status'],
            createdAt: new DateTimeImmutable($data['created_at'])->format('Y-m-d'),
            cipher: $data['cipher'],
            product: $product,
        );
    }
}
