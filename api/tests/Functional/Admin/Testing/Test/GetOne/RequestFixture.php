<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Testing\Test\GetOne;

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
    public const string TEST_ID = '2a52f226-b514-4335-acd3-873c4771a97b';
    public const string TEST_NOT_FOUND_ID = '57035df4-ad75-4ac9-acc1-2c027f239f9b';
    public const string TEST_NAME = 'Первая помощь';
    public const string TEST_CIPHER = 'ОТ 201.18';
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
            ->withCipher(self::TEST_CIPHER)
            ->withDescription(self::TEST_NAME)
            ->withSettings(new Settings(10, 10, 2))
            ->withCourseIds([CourseGetRequestFixture::COURSE_ID])
            ->withQuestionIds(CourseGetRequestFixture::QUESTION_IDS)
            ->active()
            ->build();
        $manager->persist($test);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CourseGetRequestFixture::class,
        ];
    }
}
