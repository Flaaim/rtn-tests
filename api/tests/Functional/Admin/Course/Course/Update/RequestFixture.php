<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Course\Course\Update;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Role;
use App\Auth\Test\Builder\UserBuilder;
use App\Course\Entity\Course\Answer;
use App\Course\Entity\Course\Course;
use App\Course\Entity\Course\CourseId;
use App\Course\Entity\Course\Question;
use App\Course\Entity\Course\QuestionForm;
use DateTimeImmutable;
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
        $course = new Course(
            new CourseId(self::COURSE_ID),
            self::COURSE_NAME,
            $this->getQuestions(),
            new DateTimeImmutable(),
            self::COURSE_CIPHER,
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

    private function getQuestions(): array
    {
        return [
            new Question(
                'e2ac20f6-c990-48b5-934b-2a9a7720edf8',
                'Что необходимо сделать после восстановления самостоятельного дыхания у пострадавшего с отсутствующим сознанием?',
                'https://olimpoks.hydroschool.ru/QuestionImages/c92099/9fef1bcf-9c6c-4010-a670-3dc105abc574/10/1.jpg',
                [
                    Answer::fromArray([
                        'id' => 'bbc14085f1e34ca93ccbbbd5ee9b5a01',
                        'text' => 'Потормошить пострадавшего за плечи',
                        'isCorrect' => false,
                        'answerImg' => '',
                    ]),
                    Answer::fromArray([
                        'id' => '5a81b5f1089cee2b44809bfda245da59',
                        'text' => 'Продолжить выполнять сердечно-легочную реанимацию до появления сознания у пострадавшего',
                        'isCorrect' => false,
                        'answerImg' => '',
                    ]),
                    Answer::fromArray([
                        'id' => 'a320df35029816f426dde35848e588bb',
                        'text' => 'Дать пострадавшему понюхать нашатырный спирт',
                        'isCorrect' => false,
                        'answerImg' => '',
                    ]),
                    Answer::fromArray([
                        'id' => '93ff5fdd3e7eeb5cc38696beac126968',
                        'text' => 'Придать пострадавшему устойчивое боковое положение',
                        'isCorrect' => true,
                        'answerImg' => '',
                    ]),
                ],
                QuestionForm::singleChoice()
            ),
            new Question(
                '384890bd-933d-454b-9b53-3c22211eb030',
                'На какое время допускается снять кровоостанавливающий жгут, если максимальное время его наложения истекло, а пострадавшего не транспортировали в медицинскую организацию?',
                '',
                [
                    Answer::fromArray([
                        'id' => '310eb8b5ef4dc79b46e3f968819d0896',
                        'text' => 'На 15 минут',
                        'isCorrect' => false,
                        'answerImg' => '',
                    ]),
                    Answer::fromArray([
                        'id' => '9f5608e80b8e5497fa0b42aaa3bbe7ae',
                        'text' => 'На 10 минут',
                        'isCorrect' => false,
                        'answerImg' => '',
                    ]),
                    Answer::fromArray([
                        'id' => '4e98b49484d3755f1c80a4665db74091',
                        'text' => 'На 30 минут',
                        'isCorrect' => false,
                        'answerImg' => '',
                    ]),
                    Answer::fromArray([
                        'id' => '66bc39ee7187f574dfb8699f74e55863',
                        'text' => 'Снимать жгут не рекомендуется',
                        'isCorrect' => true,
                        'answerImg' => '',
                    ]),
                ],
                QuestionForm::singleChoice()
            ),
        ];
    }
}
