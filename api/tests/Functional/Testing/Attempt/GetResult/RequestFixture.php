<?php

declare(strict_types=1);

namespace Tests\Functional\Testing\Attempt\GetResult;

use App\Auth\Entity\User\Email;
use App\Auth\Test\Builder\UserBuilder;
use App\Testing\Entity\Attempt\Attempt;
use App\Testing\Entity\Attempt\AttemptId;
use App\Testing\Entity\Attempt\Status;
use App\Testing\Entity\Test\Settings;
use App\Testing\Entity\Test\TestId;
use App\Testing\Test\Builder\TestBuilder;
use DateTimeImmutable;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class RequestFixture extends AbstractFixture
{
    public const string TEST_ID = '5c77e4dc-f1a2-4e6d-b1cd-9a7bdfa548ce';
    public const string TEST_NAME = 'Первая помощь';
    public const string TEST_CIPHER = 'ОТ 201.18';
    public const int TICKET_NUMBER = 1;
    public const string ATTEMPT_ID = '86732793-3874-4146-bc65-72d3fa75ddcb';
    public const string ATTEMPT_ID_NOT_FOUND = '581cae87-f0b5-4994-a6aa-5821fa2b963a';

    public const string USER_EMAIL = 'user@mail.ru';
    public const string USER_PASSWORD = 'user';
    public const string COURSE_ID = '63879491-6883-4e88-8be2-295d3d260346';
    public const string GUEST_ID = 'c8132964-a425-4e53-82d1-6509e4787db3';

    public function load(ObjectManager $manager): void
    {
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
            ->withSettings(new Settings(5, 2, 1))
            ->withCourseIds([self::COURSE_ID])
            ->withQuestionIds(['23ca06c6-6d2d-45d6-ae89-0526afdff6f4', '16b1df62-5ad0-4707-ab1c-746affd6670f'])
            ->active()
            ->build();
        $manager->persist($test);

        $attempt = new Attempt(
            new AttemptId(self::ATTEMPT_ID),
            self::TEST_ID,
            $user->getId()->getValue(),
            Status::passed(),
            new DateTimeImmutable(),
            self::TICKET_NUMBER,
            $this->getQuestionsSnapshot()
        );
        $manager->persist($attempt);

        $manager->flush();
    }

    private function getQuestionsSnapshot(): array
    {
        return [
            [
                'id' => '23ca06c6-6d2d-45d6-ae89-0526afdff6f4',
                'text' => 'Что необходимо сделать после восстановления самостоятельного дыхания у пострадавшего с отсутствующим сознанием?',
                'question_img' => '',
                'answers' => [
                    [
                        'id' => '5a81b5f1089cee2b44809bfda245da59',
                        'text' => 'Продолжить выполнять сердечно-легочную реанимацию до появления сознания у пострадавшего',
                        'isCorrect' => false,
                        'answerImg' => '',
                    ],
                    [
                        'id' => 'a320df35029816f426dde35848e588bb',
                        'text' => 'Дать пострадавшему понюхать нашатырный спирт',
                        'isCorrect' => true,
                        'answerImg' => '',
                    ],
                ],
                'form' => 'single_choice',
            ],
            [
                'id' => '16b1df62-5ad0-4707-ab1c-746affd6670f',
                'text' => 'На какое время допускается снять кровоостанавливающий жгут, если максимальное время его наложения истекло, а пострадавшего не транспортировали в медицинскую организацию?',
                'question_img' => '',
                'answers' => [
                    [
                        'id' => '310eb8b5ef4dc79b46e3f968819d0896',
                        'text' => 'На 15 минут',
                        'isCorrect' => false,
                        'answerImg' => '',
                    ],
                    [
                        'id' => '66bc39ee7187f574dfb8699f74e55863',
                        'text' => 'Снимать жгут не рекомендуется',
                        'isCorrect' => true,
                        'answerImg' => '',
                    ],
                ],
                'form' => 'single_choice',
            ],
        ];
    }
}
