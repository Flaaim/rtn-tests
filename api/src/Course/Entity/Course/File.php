<?php

declare(strict_types=1);

namespace App\Course\Entity\Course;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final class File
{
    public function __construct(
        #[ORM\Column(type: 'string', length: 255, nullable: true)]
        private string $filePath,
        #[ORM\Column(type: 'string', length: 255, nullable: true)]
        private string $name,
    ) {}

    public function getPath(): string
    {
        return $this->filePath;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
