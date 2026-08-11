<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Course\Course\Get;

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
    public const string COURSE_ID = '63879491-6883-4e88-8be2-295d3d260346';
    public const string COURSE_NOT_FOUND_ID = '646140a8-f49e-4514-bf7e-da3ee0d6fd80';
    public const string COURSE_NAME = 'Первая помощь';
    public const string COURSE_CIPHER = 'ОТ 201.18';
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

        $course = new Course(
            new CourseId(self::COURSE_ID),
            self::COURSE_NAME,
            $this->getQuestions(),
            new DateTimeImmutable(),
            self::COURSE_CIPHER
        );

        $course->releaseEvents();

        $manager->persist($course);
        $manager->flush();
    }

    private function getQuestions(): array
    {
        return [
            new Question(
                '90be077454a14f3d965c4b07645e3769',
                'Что необходимо сделать после восстановления самостоятельного дыхания у пострадавшего с отсутствующим сознанием?',
                '',
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
                '6724ac7652bc47d6913ab8ca11b2ea36',
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
