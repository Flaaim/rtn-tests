<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Testing\Course\Add;

use App\Testing\Entity\Course\Answer;
use App\Testing\Entity\Course\CourseId;
use App\Testing\Entity\Course\CourseRepository;
use App\Testing\Entity\Course\Question;
use App\Testing\Event\CourseCreated;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;
use Tests\Functional\ArraySubsetAssertTrait;
use Tests\Functional\FixturesLoader;
use Tests\Functional\Json;
use Tests\Functional\OAuthTokenTrait;

/**
 * @internal
 * @coversNothing
 */
final class RequestActionTest extends WebTestCase
{
    use ArraySubsetAssertTrait;
    use OAuthTokenTrait;
    private readonly KernelBrowser $client;
    private readonly CourseRepository $courses;
    private string $adminToken;
    private string $userToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
        $this->client->disableReboot();

        $container = $this->client->getContainer();
        /** @var EntityManagerInterface $em */
        $em = $container->get(EntityManagerInterface::class);
        $this->courses = new CourseRepository($em);

        $fixturesLoader = new FixturesLoader($container);
        $fixturesLoader->loadFixtures([RequestFixture::class]);

        $this->adminToken = $this->getAccessToken(
            $this->client,
            RequestFixture::ADMIN_EMAIL,
            RequestFixture::ADMIN_PASSWORD,
        );

        $this->userToken = $this->getAccessToken(
            $this->client,
            RequestFixture::USER_EMAIL,
            RequestFixture::USER_PASSWORD,
        );
    }

    public function testUnauthenticatedReturns401(): void
    {
        $this->client->jsonRequest('POST', '/v1/admin/testing/courses', [
            'name' => RequestFixture::COURSE_NAME,
            'draft' => $this->getDraft(),
        ]);

        self::assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testForbiddenForRegularUser(): void
    {
        $this->client->jsonRequest(
            'POST',
            '/v1/admin/testing/courses',
            [
                'name' => RequestFixture::COURSE_NAME,
                'draft' => $this->getDraft(),
            ],
            $this->authHeaders($this->userToken),
        );

        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testSuccess(): void
    {
        /** @var InMemoryTransport $transport */
        $transport = $this->client->getContainer()->get('messenger.transport.async');
        $transport->reset();

        $this->client->jsonRequest(
            'POST',
            '/v1/admin/testing/courses',
            [
                'name' => RequestFixture::COURSE_NAME,
                'draft' => $this->getDraft(),
                'cipher' => RequestFixture::COURSE_CIPHER,
            ],
            $this->authHeaders($this->adminToken),
        );

        self::assertEquals(201, $this->client->getResponse()->getStatusCode());

        self::assertCount(1, $transport->getSent());

        $message = $transport->getSent()[0]->getMessage();
        self::assertInstanceOf(CourseCreated::class, $message);

        $course = $this->courses->get(new CourseId($message->id));
        self::assertEquals(RequestFixture::COURSE_NAME, $course->getName());
        self::assertEquals($this->getExpectedResult()[0]->getText(), $course->getQuestions()[0]->getText());
        self::assertEquals($this->getExpectedResult()[1]->getText(), $course->getQuestions()[1]->getText());
    }

    public function testEmpty(): void
    {
        $this->client->jsonRequest('POST', '/v1/admin/testing/courses', [], $this->authHeaders($this->adminToken));

        self::assertEquals(422, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());
        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'name' => 'This value should not be blank.',
            'draft' => 'This value should not be blank.',
            'cipher' => 'This value should not be blank.',
        ]], $data);
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

    private function getExpectedResult(): array
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
                ]
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
                ]
            ),
        ];
    }
}
