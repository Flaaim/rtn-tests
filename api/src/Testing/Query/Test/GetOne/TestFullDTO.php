<?php

declare(strict_types=1);

namespace App\Testing\Query\Test\GetOne;

use DateTimeImmutable;

final class TestFullDTO
{
    /**
     * @param CourseDTO[] $courses
     */
    public function __construct(
        public string $id,
        public string $name,
        public string $cipher,
        public string $description,
        public array $courses,
        public array $tickets,
        public string $slug,
        public string $createdAt,
        public string $status,
        public array $settings
    ) {}

    public static function fromArray(array $data): self
    {
        $courses = array_map(
            static fn (array $courseData) => CourseDTO::fromArray($courseData),
            $data['courses'] ?? []
        );

        $tickets = array_map(
            static fn (array $ticketData) => TicketDTO::fromArray($ticketData),
            $data['tickets'] ?? []
        );

        return new self(
            id: $data['id'],
            name: $data['name'],
            cipher: $data['cipher'],
            description: $data['description'],
            courses: $courses,
            tickets: $tickets,
            slug: $data['slug'],
            createdAt: new DateTimeImmutable($data['created_at'])->format('Y-m-d'),
            status: $data['status'],
            settings: $data['settings'] ?? []
        );
    }
}
