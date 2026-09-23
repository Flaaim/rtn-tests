<?php

declare(strict_types=1);

namespace App\Testing\Entity\Category;

use App\SharedDomain\AggregateRoot;
use App\SharedDomain\Event\EventTrait;
use Doctrine\ORM\Mapping as ORM;
use DomainException;

#[ORM\Entity]
#[ORM\Table(name: 'test_categories')]
final class Category implements AggregateRoot
{
    use EventTrait;

    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'category_id', unique: true)]
        private CategoryId $id,
        #[ORM\Column(type: 'string', length: 255)]
        private string $name,
        #[ORM\Column(type: 'text')]
        private string $description,
        #[ORM\Column(type: 'string', length: 255, unique: true)]
        private string $slug,
        #[ORM\Column(type: 'string', nullable: true)]
        private ?string $parentId = null,
    ) {}

    public function getId(): CategoryId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getParentId(): ?string
    {
        return $this->parentId;
    }

    public function rename(string $name, string $slug): void
    {
        $this->name = $name;
        $this->slug = $slug;
    }

    public function move(?string $parentId): void
    {
        if ($this->id->getValue() === $parentId) {
            throw new DomainException('Category cannot be a parent of itself.');
        }
        $this->parentId = $parentId;
    }

    public function changeDescription(string $description): void
    {
        $this->description = $description;
    }
}
