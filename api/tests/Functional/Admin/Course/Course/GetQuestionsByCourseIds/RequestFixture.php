<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Course\Course\GetQuestionsByCourseIds;

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

    public const string COURSE_ANOTHER_ID = '89683793-285f-4741-93bd-a5f3b9bf6ea9';
    public const string COURSE_NOT_FOUND_ID = 'f7175661-ac7b-4467-9d6c-7f0a588a144e';
    public const string COURSE_CIPHER = 'ОТ 201.18';
    public const string COURSE_ANOTHER_CIPHER = '';
    public const string COURSE_NAME = 'Первая помощь';
    public const string COURSE_ANOTHER_NAME = 'Основы промышленной безопасности';
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

        $anotherCourse = new Course(
            new CourseId(self::COURSE_ANOTHER_ID),
            self::COURSE_ANOTHER_NAME,
            $this->anotherQuestions(),
            new DateTimeImmutable(),
            self::COURSE_ANOTHER_CIPHER,
        );
        $anotherCourse->releaseEvents();
        $manager->persist($anotherCourse);

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
                '90be077454a14f3d965c4b07645e3769',
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

    private function anotherQuestions(): array
    {
        return [
            new Question(
                '2fb7dadd-c74c-4d6e-8c85-956076e2d093',
                'На какой высоте при работе с приставной лестницы следует применять страховочную систему, прикрепляемую к конструкции сооружения или к лестнице?',
                '',
                [
                    Answer::fromArray([
                        'id' => 'bbc14085f1e34ca93ccbbbd5ee9b5a01',
                        'text' => 'Если вывешены плакаты "Не включать. Работают люди"',
                        'isCorrect' => false,
                        'answerImg' => '',
                    ]),
                    Answer::fromArray([
                        'id' => '5a81b5f1089cee2b44809bfda245da59',
                        'text' => 'Если включено сигнальное освещение мачты',
                        'isCorrect' => false,
                        'answerImg' => '',
                    ]),
                    Answer::fromArray([
                        'id' => 'a320df35029816f426dde35848e588bb',
                        'text' => 'Если отключен прогрев антенн',
                        'isCorrect' => false,
                        'answerImg' => '',
                    ]),
                    Answer::fromArray([
                        'id' => '93ff5fdd3e7eeb5cc38696beac126968',
                        'text' => 'Если работник имеет группу по электробезопасности ниже IV',
                        'isCorrect' => true,
                        'answerImg' => '',
                    ]),
                ],
                QuestionForm::singleChoice()
            ),
            new Question(
                '83f097fd-a2f5-42c5-a5a5-298e68bfad7e',
                'Укажите, что должны сделать работники, если внезапно поднялся порывистый ветер и начал сильно раскачивать подвесную люльку, а у работников отсутствует прибор (анемометр) для измерения скорости ветра.',
                '',
                [
                    Answer::fromArray([
                        'id' => '310eb8b5ef4dc79b46e3f968819d0896',
                        'text' => 'Не приступать к выполнению работ и сообщить о случившемся уполномоченному по охране труда',
                        'isCorrect' => false,
                        'answerImg' => '',
                    ]),
                    Answer::fromArray([
                        'id' => '9f5608e80b8e5497fa0b42aaa3bbe7ae',
                        'text' => 'Приступить к работам на высоте и по окончании смены сообщить о случившемся вышестоящему руководству',
                        'isCorrect' => false,
                        'answerImg' => '',
                    ]),
                    Answer::fromArray([
                        'id' => '4e98b49484d3755f1c80a4665db74091',
                        'text' => 'Приступить к выполнению работ только в том случае, если ответственный за проведение работ в наряде-допуске сделает соответствующую запись о том, что выполнение всех подготовительных мероприятий не требуется',
                        'isCorrect' => false,
                        'answerImg' => '',
                    ]),
                    Answer::fromArray([
                        'id' => '66bc39ee7187f574dfb8699f74e55863',
                        'text' => 'Не приступать к работам на высоте до выполнения всех указанных в наряде-допуске мероприятий или сообщить о случившемся вышестоящему руководству',
                        'isCorrect' => true,
                        'answerImg' => '',
                    ]),
                ],
                QuestionForm::singleChoice()
            ),
        ];
    }
}
