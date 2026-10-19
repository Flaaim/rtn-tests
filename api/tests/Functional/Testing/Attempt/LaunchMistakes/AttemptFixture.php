<?php

declare(strict_types=1);

namespace Tests\Functional\Testing\Attempt\LaunchMistakes;

use App\Testing\Entity\Attempt\Answer;
use App\Testing\Entity\Attempt\AnswerId;
use App\Testing\Entity\Attempt\Attempt;
use App\Testing\Entity\Attempt\AttemptId;
use App\Testing\Entity\Attempt\Status;
use DateTimeImmutable;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class AttemptFixture extends AbstractFixture
{
    public const string ATTEMPT_ID = '86732793-3874-4146-bc65-72d3fa75ddcb';
    public const int TICKET_NUMBER = 1;

    public function load(ObjectManager $manager): void
    {
        $attempt = new Attempt(
            new AttemptId(self::ATTEMPT_ID),
            RequestFixture::TEST_ID,
            UserFixture::USER_ID,
            Status::passed(),
            new DateTimeImmutable(),
            self::TICKET_NUMBER,
            $this->getQuestionsSnapshot(),
            10,
            2
        );
        $manager->persist($attempt);

        $answer1 = new Answer(
            AnswerId::generate(),
            '90be077454a14f3d965c4b07645e3769',
            ['7dc4c64f-0175-4650-a108-17e2842c757f'],
            false
        );
        $answer1->appendAttempt($attempt);

        $answer2 = new Answer(
            AnswerId::generate(),
            '6724ac7652bc47d6913ab8ca11b2ea36',
            ['228001f9-eb8e-448f-b81a-c6f2443508c1'],
            false
        );
        $answer2->appendAttempt($attempt);

        $manager->persist($answer1);
        $manager->persist($answer2);

        $manager->flush();
    }

    private function getQuestionsSnapshot(): array
    {
        return [
            [
                'id' => '90be077454a14f3d965c4b07645e3769',
                'text' => 'Что необходимо сделать после восстановления самостоятельного дыхания у пострадавшего с отсутствующим сознанием?',
                'question_img' => '',
                'answers' => [
                    [
                        'id' => '7dc4c64f-0175-4650-a108-17e2842c757f',
                        'text' => 'Продолжить выполнять сердечно-легочную реанимацию до появления сознания у пострадавшего',
                        'isCorrect' => false,
                        'answerImg' => '',
                    ],
                    [
                        'id' => '6efcc061-770e-473f-bc8b-b70ac789d901',
                        'text' => 'Дать пострадавшему понюхать нашатырный спирт',
                        'isCorrect' => true,
                        'answerImg' => '',
                    ],
                ],
                'form' => 'single_choice',
            ],
            [
                'id' => '6724ac7652bc47d6913ab8ca11b2ea36',
                'text' => 'На какое время допускается снять кровоостанавливающий жгут, если максимальное время его наложения истекло, а пострадавшего не транспортировали в медицинскую организацию?',
                'question_img' => '',
                'answers' => [
                    [
                        'id' => '228001f9-eb8e-448f-b81a-c6f2443508c1',
                        'text' => 'На 15 минут',
                        'isCorrect' => false,
                        'answerImg' => '',
                    ],
                    [
                        'id' => 'd6293ff8-1108-4ae6-9e66-44cb95f360b2',
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
