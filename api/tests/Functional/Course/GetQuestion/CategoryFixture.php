<?php

declare(strict_types=1);

namespace Tests\Functional\Course\GetQuestion;

use App\Testing\Entity\Category\Category;
use App\Testing\Entity\Category\CategoryId;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use DomainException;
use Symfony\Component\String\Slugger\AsciiSlugger;

final class CategoryFixture extends AbstractFixture
{
    public const string CATEGORY_ID = '753ac2cb-6f03-4c7d-a68c-f14646503c67';
    public const string CATEGORY_PARENT_ID = '1d655786-d282-4b08-ae39-257408dd7ed9';
    public const string CATEGORY_NAME = 'Охрана труда';
    public const string CATEGORY_PARENT_NAME = 'Другое название';

    public function load(ObjectManager $manager): void
    {
        $parent = new Category(
            new CategoryId(self::CATEGORY_PARENT_ID),
            self::CATEGORY_PARENT_NAME,
            'Category description',
            $this->generateSlug(self::CATEGORY_PARENT_NAME),
        );
        $manager->persist($parent);

        $child = new Category(
            new CategoryId(self::CATEGORY_ID),
            self::CATEGORY_NAME,
            'Category description',
            $this->generateSlug(self::CATEGORY_NAME),
            $parent->getId()->getValue(),
        );
        $manager->persist($child);

        $manager->flush();
    }

    private function generateSlug(string $value): string
    {
        $slugger = new AsciiSlugger();

        $slug = $slugger->slug($value)->lower()->toString();

        if ('' === $slug) {
            throw new DomainException('Cannot generate slug from the given name.');
        }

        return $slug;
    }
}
