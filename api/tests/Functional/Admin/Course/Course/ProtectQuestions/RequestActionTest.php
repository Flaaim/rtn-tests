<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Course\Course\ProtectQuestions;

use App\Course\Entity\Course\CourseId;
use App\Course\Entity\Course\CourseRepository;
use App\Course\Event\QuestionsProtected;
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
            '/v1/admin/testing/courses/' . RequestFixture::COURSE_ID . '/protect-questions',
        );

        self::assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testForbiddenForRegularUsers(): void
    {
        $this->client->jsonRequest(
            'PUT',
            '/v1/admin/testing/courses/' . RequestFixture::COURSE_ID . '/protect-questions',
            [],
            $this->authHeaders($this->userToken)
        );

        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testSuccess(): void
    {
        /** @var InMemoryTransport $transport */
        $transport = $this->container->get('messenger.transport.async_outbox');
        $transport->reset();

        $this->client->jsonRequest(
            'PUT',
            '/v1/admin/testing/courses/' . RequestFixture::COURSE_ID . '/protect-questions',
            [],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(204, $this->client->getResponse()->getStatusCode());

        $course = $this->courses->get(new CourseId(RequestFixture::COURSE_ID));

        $questions = $course->getQuestions();

        self::assertTrue($questions[0]->isProtected());
        self::assertTrue($questions[1]->isProtected());

        $message = $transport->getSent()[0]->getMessage();
        self::assertInstanceOf(QuestionsProtected::class, $message);

        self::assertEquals(RequestFixture::COURSE_ID, $message->courseId);
        self::assertEquals(RequestFixture::COURSE_CIPHER, $message->cipher);
    }

    public function testNotFound(): void
    {
        $this->client->jsonRequest(
            'PUT',
            '/v1/admin/testing/courses/' . RequestFixture::COURSE_NOT_FOUND_ID . '/protect-questions',
            [],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(409, $this->client->getResponse()->getStatusCode());
        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['message' => 'Course not found.'], $data);
    }

    public function testInvalid(): void
    {
        $this->client->jsonRequest(
            'PUT',
            '/v1/admin/testing/courses/ivalid/protect-questions',
            [],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(422, $this->client->getResponse()->getStatusCode());
        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'courseId' => 'This is not a valid UUID.',
        ]], $data);
    }
}
