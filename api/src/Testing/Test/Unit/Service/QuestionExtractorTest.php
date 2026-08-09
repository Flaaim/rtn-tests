<?php

declare(strict_types=1);

namespace App\Testing\Test\Unit\Service;

use App\Testing\Entity\Course\Answer;
use App\Testing\Entity\Course\Question;
use App\Testing\Entity\Course\QuestionForm;
use App\Testing\Service\QuestionExtractor;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class QuestionExtractorTest extends TestCase
{
    public function testExtract(): void
    {
        $draft = $this->getDraft();
        $extractor = new QuestionExtractor();
        $result = $extractor->extract($draft);
        self::assertEquals($this->getResult(), $result);
    }

    private function getDraft(): string
    {
        return '[
          {
            "id": "90be077454a14f3d965c4b07645e3769",
            "number": 1,
            "text": "Что необходимо сделать после восстановления самостоятельного дыхания у пострадавшего с отсутствующим сознанием?",
            "questionImg": "",
            "answers": [
              {
                "id": "bbc14085f1e34ca93ccbbbd5ee9b5a01",
                "text": "Потормошить пострадавшего за плечи",
                "isCorrect": false,
                "answerImg": ""
              },
              {
                "id": "5a81b5f1089cee2b44809bfda245da59",
                "text": "Продолжить выполнять сердечно-легочную реанимацию до появления сознания у пострадавшего",
                "isCorrect": false,
                "answerImg": ""
              },
              {
                "id": "a320df35029816f426dde35848e588bb",
                "text": "Дать пострадавшему понюхать нашатырный спирт",
                "isCorrect": false,
                "answerImg": ""
              },
              {
                "id": "93ff5fdd3e7eeb5cc38696beac126968",
                "text": "Придать пострадавшему устойчивое боковое положение",
                "isCorrect": true,
                "answerImg": ""
              }
            ]
          },
          {
            "id": "6724ac7652bc47d6913ab8ca11b2ea36",
            "number": 2,
            "text": "На какое время допускается снять кровоостанавливающий жгут, если максимальное время его наложения истекло, а пострадавшего не транспортировали в медицинскую организацию?",
            "questionImg": "",
            "answers": [
              {
                "id": "310eb8b5ef4dc79b46e3f968819d0896",
                "text": "На 15 минут",
                "isCorrect": false,
                "answerImg": ""
              },
              {
                "id": "9f5608e80b8e5497fa0b42aaa3bbe7ae",
                "text": "На 10 минут",
                "isCorrect": false,
                "answerImg": ""
              },
              {
                "id": "4e98b49484d3755f1c80a4665db74091",
                "text": "На 30 минут",
                "isCorrect": false,
                "answerImg": ""
              },
              {
                "id": "66bc39ee7187f574dfb8699f74e55863",
                "text": "Снимать жгут не рекомендуется",
                "isCorrect": true,
                "answerImg": ""
              }
            ]
          }
        ]';
    }

    private function getResult(): array
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
