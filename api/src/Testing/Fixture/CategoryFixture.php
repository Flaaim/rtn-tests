<?php

declare(strict_types=1);

namespace App\Testing\Fixture;

use App\Testing\Entity\Category\Category;
use App\Testing\Entity\Category\CategoryId;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class CategoryFixture extends AbstractFixture
{
    public const string CATEGORY_ID = '199f466e-a593-45be-9bdb-959c06ed5572';

    public function load(ObjectManager $manager): void
    {
        $parent = new Category(
            CategoryId::generate(),
            'Охрана труда',
            'Список актуальных тестов по направлению охрана труда',
            'ohrana-truda'
        );
        $manager->persist($parent);

        $child = new Category(
            new CategoryId(self::CATEGORY_ID),
            'Общие вопросы охраны труда',
            'Список актуальных тестов по направлению общие вопросы охраны труда',
            'obshie-voprosy',
            $parent->getId()->getValue()
        );
        $manager->persist($child);
        $manager->flush();
    }
}
