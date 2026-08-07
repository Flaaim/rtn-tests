<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Testing\Course\Rename;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Role;
use App\Auth\Test\Builder\UserBuilder;
use App\Testing\Entity\Course\Course;
use App\Testing\Entity\Course\CourseId;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class RequestFixture extends AbstractFixture
{
    public const string COURSE_ID = '63879491-6883-4e88-8be2-295d3d260346';
    public const string COURSE_NOT_FOUND_ID = '1872768c-65ee-4b03-8d01-0fe8f91da2c9';
    public const string COURSE_NAME = 'Первая помощь';
    public const string COURSE_CIPHER = 'ОТ 201.18';

    public const string ADMIN_EMAIL = 'admin@mail.ru';
    public const string ADMIN_PASSWORD = 'admin';

    public const string USER_EMAIL = 'user@mail.ru';
    public const string USER_PASSWORD = 'user';

    public function load(ObjectManager $manager): void
    {
        $course = new Course(
            new CourseId(self::COURSE_ID),
            self::COURSE_NAME,
            new ArrayCollection([]),
            new DateTimeImmutable(),
            self::COURSE_CIPHER
        );

        $course->releaseEvents();

        $manager->persist($course);

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
        $manager->flush();
    }
}
