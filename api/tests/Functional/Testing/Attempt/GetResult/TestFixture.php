<?php

declare(strict_types=1);

namespace Tests\Functional\Testing\Attempt\GetResult;

use App\Testing\Entity\Category\Category;
use App\Testing\Entity\Category\CategoryId;
use App\Testing\Entity\Test\Settings;
use App\Testing\Entity\Test\TestId;
use App\Testing\Test\Builder\TestBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use DomainException;
use Symfony\Component\String\Slugger\AsciiSlugger;

final class TestFixture extends AbstractFixture
{
    public const string TEST_ID = '5c77e4dc-f1a2-4e6d-b1cd-9a7bdfa548ce';
    public const string TEST_NAME = 'Первая помощь';
    public const string TEST_CIPHER = 'ОТ 201.18';
    public const int TICKET_NUMBER = 1;

    public const string COURSE_ID = '63879491-6883-4e88-8be2-295d3d260346';
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

        $test = new TestBuilder()
            ->withId(new TestId(self::TEST_ID))
            ->withCategoryId(self::CATEGORY_ID)
            ->withName(self::TEST_NAME)
            ->withCipher(self::TEST_CIPHER)
            ->withDescription(self::TEST_NAME)
            ->withSettings(new Settings(5, 2, 1))
            ->withCourseIds([self::COURSE_ID])
            ->withQuestionIds(['23ca06c6-6d2d-45d6-ae89-0526afdff6f4', '16b1df62-5ad0-4707-ab1c-746affd6670f'])
            ->active()
            ->build();
        $manager->persist($test);

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
