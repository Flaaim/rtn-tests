<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Testing\Test\UpdateSettings;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Role;
use App\Auth\Test\Builder\UserBuilder;
use App\Testing\Entity\Test\Settings;
use App\Testing\Entity\Test\TestId;
use App\Testing\Test\Builder\TestBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Tests\Functional\Admin\Course\Course\Get\RequestFixture as CourseGetRequestFixture;

final class RequestFixture extends AbstractFixture implements DependentFixtureInterface
{
    public const string TEST_ID = 'ff1f1a44-f5f5-4956-9a09-feef0d28cedd';
    public const string TEST_NOT_FOUND_ID = '96861841-ad71-467d-b32b-2f0e539de742';

    public const string TEST_ACTIVE_ID = '958467b2-37c1-4301-bb69-df3325aff49c';
    public const string TEST_NAME = 'Первая помощь';
    public const string TEST_CIPHER = 'ОТ 201.18';
    public const string TEST_CIPHER_ACTIVE = 'ПБ 115.26';
    public const string ADMIN_EMAIL = 'admin@mail.ru';
    public const string ADMIN_PASSWORD = 'admin';

    public const string USER_EMAIL = 'user@mail.ru';
    public const string USER_PASSWORD = 'user';

    public function load(ObjectManager $manager): void
    {
        $admin = new UserBuilder()
            ->withEmail(new Email(self::ADMIN_EMAIL))
            ->withPassword(self::ADMIN_PASSWORD)
            ->withRole(Role::admin())
            ->active()
            ->build();
        $manager->persist($admin);

        $user = new UserBuilder()
            ->withEmail(new Email(self::USER_EMAIL))
            ->withPassword(self::USER_PASSWORD)
            ->active()
            ->build();
        $manager->persist($user);

        $test = new TestBuilder()
            ->withId(new TestId(self::TEST_ID))
            ->withName(self::TEST_NAME)
            ->withSettings(new Settings(10, 10, 2))
            ->withCourseIds([CourseGetRequestFixture::COURSE_ID])
            ->withQuestionIds([
                '5cf0b271-6e2b-4be9-b0b6-d0674947f0b4',
                'ec04c252-dfeb-42b3-9b40-f3da96b0dd95',
                'e06bdbed-546a-4cf2-a3f6-c8debe6cb7c5',
                'd69b9c33-91c8-4774-b5e5-1e86c5550d35',
                '847a840d-33b3-4050-ac82-0c08bc736d3c',
                'a4bf5020-1543-40fd-bc04-beaf89472560',
                'fa3fab04-72ad-44ba-9363-208f56e97f84',
                '4e9714f7-c8b3-48b5-985d-d32972a725e9',
                'fe3d1062-59ca-4648-8ca2-0ebab6f16e30',
                'ad1388a2-ae3d-4d6d-bd39-0fc74c927acd',
            ])
            ->build();
        $manager->persist($test);

        $testActive = new TestBuilder()
            ->withId(new TestId(self::TEST_ACTIVE_ID))
            ->withName(self::TEST_NAME)
            ->withCipher(self::TEST_CIPHER_ACTIVE)
            ->active()
            ->build();
        $manager->persist($testActive);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CourseGetRequestFixture::class,
        ];
    }
}
