<?php

declare(strict_types=1);

namespace App\Testing\Fixture;

use App\Testing\Entity\Test\Settings;
use App\Testing\Entity\Test\Test;
use App\Testing\Entity\Test\TestId;
use DateTimeImmutable;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/** @psalm-suppress UnusedClass */
final class TestFixture extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $test = new Test(
            TestId::generate(),
            CategoryFixture::CATEGORY_ID,
            'Вредные и опасные производственные факторы',
            'ВОПФ 884.1',
            'Тест для проверки знаний работников безопасным методам',
            ['a22a2b1d-64ed-459d-aef8-b437da27bda3'],
            [
                'e9fdcede-efae-4b7d-822f-cf55a3dfb8d8',
                '79e023c0-31cc-465d-a5a2-055db8eb6bc6',
                'c7087ae0-d49a-4fdc-99d9-88f412355672',
                '18b84299-9b63-423c-9da5-c06a5e1602a1',
                '3304cd01-cac9-4280-963c-d7850ca04f1d',
            ],
            'vopf-884',
            new DateTimeImmutable(),
            new Settings(2, 3, 2)
        );
        $test->activate();
        $manager->persist($test);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixture::class,
        ];
    }
}
