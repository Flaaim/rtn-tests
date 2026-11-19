<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Course\Course\Protect;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Role;
use App\Auth\Test\Builder\UserBuilder;
use App\Course\Entity\Course\Answer;
use App\Course\Entity\Course\CourseId;
use App\Course\Entity\Course\Question;
use App\Course\Entity\Course\QuestionForm;
use App\Course\Test\Builder\CourseBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class RequestFixture extends AbstractFixture
{
    public const string COURSE_ID = 'dddbdff1-4228-402b-93c1-73e23f686f7c';
    public const string COURSE_NOT_FOUND_ID = '1872768c-65ee-4b03-8d01-0fe8f91da2c9';
    public const string COURSE_CIPHER = 'ОТ 201.18';
    public const string COURSE_NAME = 'Первая помощь';

    public const string ADMIN_EMAIL = 'admin@mail.ru';
    public const string ADMIN_PASSWORD = 'admin';

    public const string USER_EMAIL = 'user@mail.ru';
    public const string USER_PASSWORD = 'user';

    public function load(ObjectManager $manager): void
    {
        $course = new CourseBuilder()
            ->withCourseId(new CourseId(self::COURSE_ID))
            ->withName(self::COURSE_NAME)
            ->withCipher(self::COURSE_CIPHER)
            ->build();
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
