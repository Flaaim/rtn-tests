<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Course\Course\Update;

use App\Course\Entity\Course\CourseId;
use App\Course\Entity\Course\CourseRepository;
use App\Course\Entity\Course\Question;
use App\Course\Event\CourseUpdated;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;
use Tests\Functional\FixturesLoader;
use Tests\Functional\Json;
use Tests\Functional\OAuthTokenTrait;

/**
 * @internal
 * @coversNothing
 */
final class RequestActionTest extends WebTestCase
{
    use OAuthTokenTrait;
    private readonly KernelBrowser $client;
    private readonly ContainerInterface $container;

    private readonly CourseRepository $courses;
    private readonly string $adminToken;
    private readonly string $userToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
        $this->client->disableReboot();

        $this->container = $this->client->getContainer();
        $fixturesLoader = new FixturesLoader($this->container);
        $fixturesLoader->loadFixtures([RequestFixture::class]);

        /** @var EntityManagerInterface $em */
        $em = $this->container->get(EntityManagerInterface::class);
        $this->courses = new CourseRepository($em);

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
        $this->client->jsonRequest(
            'PUT',
            '/v1/admin/testing/courses/' . RequestFixture::COURSE_ID . '/update'
        );

        self::assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testForbiddenForRegularUsers(): void
    {
        $this->client->jsonRequest(
            'PUT',
            '/v1/admin/testing/courses/' . RequestFixture::COURSE_ID . '/update',
            [
                'draft' => $this->getDraft(),
            ],
            $this->authHeaders($this->userToken)
        );

        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testSuccess(): void
    {
        /** @var InMemoryTransport $transport */
        $transport = $this->client->getContainer()->get('messenger.transport.async');
        $transport->reset();

        $this->client->jsonRequest(
            'PUT',
            '/v1/admin/testing/courses/' . RequestFixture::COURSE_ID . '/update',
            [
                'draft' => $this->getDraft(),
            ],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(204, $this->client->getResponse()->getStatusCode());

        self::assertCount(1, $transport->getSent());
        $message = $transport->getSent()[0]->getMessage();

        self::assertInstanceOf(CourseUpdated::class, $message);

        $course = $this->courses->get(new CourseId($message->id));

        /** @var Question[] $questions */
        $questions = $course->getQuestions();

        self::assertCount(2, $questions);
        self::assertEquals('f99050cc-e8a8-4e3b-ae49-bd8734adbc03', $questions[0]->getId());
        self::assertEquals('a11050cc-e8a8-4e3b-ae49-bd8734adbc99', $questions[1]->getId());
    }

    public function testEmpty(): void
    {
        $this->client->jsonRequest(
            'PUT',
            '/v1/admin/testing/courses/' . RequestFixture::COURSE_ID . '/update',
            [],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(422, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'draft' => 'This value should not be blank.',
        ]], $data);
    }

    public function testNotFound(): void
    {
        $this->client->jsonRequest(
            'PUT',
            '/v1/admin/testing/courses/' . RequestFixture::COURSE_NOT_FOUND_ID . '/update',
            [
                'draft' => $this->getDraft(),
            ],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(409, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());
        $data = Json::decode($body);

        self::assertEquals([
            'message' => 'Course not found.',
        ], $data);
    }

    private function getDraft(): string
    {
        return '[
          {
            "id": "f99050cc-e8a8-4e3b-ae49-bd8734adbc03",
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
            "id": "a11050cc-e8a8-4e3b-ae49-bd8734adbc99",
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
}
