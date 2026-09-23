<?php

declare(strict_types=1);

namespace App\Testing\Test\Unit\Entity\Category;

use App\Testing\Entity\Category\Category;
use App\Testing\Entity\Category\CategoryId;
use DomainException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class CategoryTest extends TestCase
{
    public function testCreate(): void
    {
        $parentCat = new Category(
            $id = CategoryId::generate(),
            $name = 'name',
            $description = 'description',
            $slug = 'slug',
        );

        self::assertEquals($id->getValue(), $parentCat->getId()->getValue());
        self::assertEquals($name, $parentCat->getName());
        self::assertEquals($description, $parentCat->getDescription());
        self::assertEquals($slug, $parentCat->getSlug());
        self::assertNull($parentCat->getParentId());
    }

    public function testRename(): void
    {
        $cat = new Category(
            CategoryId::generate(),
            'name',
            'description',
            'name',
        );

        $cat->rename('newName', 'newSlug');

        self::assertEquals('newName', $cat->getName());
        self::assertEquals('newSlug', $cat->getSlug());
    }

    public function testMove(): void
    {
        $cat = new Category(
            CategoryId::generate(),
            'name',
            'description',
            'name',
        );

        $parentCat = new Category(
            CategoryId::generate(),
            'parent',
            'parent',
            'parent',
        );

        $id = $parentCat->getId()->getValue();
        $cat->move($id);

        self::assertNotNull($cat->getParentId());
    }

    public function testParentItself(): void
    {
        $cat = new Category(
            CategoryId::generate(),
            'name',
            'description',
            'name',
        );
        $id = $cat->getId()->getValue();

        self::expectException(DomainException::class);
        self::expectExceptionMessage('Category cannot be a parent of itself.');
        $cat->move($id);
    }

    public function testChangeDescription(): void
    {
        $cat = new Category(
            CategoryId::generate(),
            'name',
            'description',
            'name',
        );

        $cat->changeDescription('newDescription');

        self::assertEquals('newDescription', $cat->getDescription());
    }
}
