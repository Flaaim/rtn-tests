<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Testing\Test\Update;

use App\Testing\Entity\Test\TestId;
use App\Testing\Entity\Test\TestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tests\Functional\Admin\Course\Course\Get\RequestFixture as CourseGetRequestFixture;
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

    private readonly TestRepository $tests;

    private string $adminToken;
    private string $userToken;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        $this->container = $this->client->getContainer();

        $fixturesLoader = new FixturesLoader($this->container);
        $fixturesLoader->loadFixtures([RequestFixture::class]);

        /** @var EntityManagerInterface $em */
        $em = $this->container->get(EntityManagerInterface::class);
        $this->tests = new TestRepository($em);

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
        $this->client->jsonRequest('PUT', '/v1/admin/testing/tests/' . RequestFixture::TEST_ID . '/update');

        self::assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testForbiddenForRegularUsers(): void
    {
        $this->client->jsonRequest(
            'PUT',
            '/v1/admin/testing/tests/' . RequestFixture::TEST_ID . '/update',
            [],
            $this->authHeaders($this->userToken)
        );

        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testSuccess(): void
    {
        $this->client->jsonRequest(
            'PUT',
            '/v1/admin/testing/tests/' . RequestFixture::TEST_ID . '/update',
            [
                'courseIds' => [CourseGetRequestFixture::COURSE_ANOTHER_ID],
            ],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(204, $this->client->getResponse()->getStatusCode());

        $test = $this->tests->get(new TestId(RequestFixture::TEST_ID));

        $tickets = $test->getTickets();

        self::assertCount(5, $tickets);

        self::assertEqualsCanonicalizing(CourseGetRequestFixture::QUESTION_ANOTHER_IDS, $tickets[0]->questionIds);
        self::assertEquals([CourseGetRequestFixture::COURSE_ANOTHER_ID], $test->getCourseId());
    }

    public function testCourseQuestionsEmpty(): void
    {
        $this->client->jsonRequest(
            'PUT',
            '/v1/admin/testing/tests/' . RequestFixture::TEST_ID . '/update',
            [
                'courseIds' => ['4f766a24-ea22-4237-924f-5c47b4e6ea5f'], // not found,
            ],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(409, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['message' => 'QuestionIds not found.'], $data);
    }

    public function testNotFound(): void
    {
        $this->client->jsonRequest(
            'PUT',
            '/v1/admin/testing/tests/' . RequestFixture::TEST_NOT_FOUND_ID . '/update',
            [],
            $this->authHeaders($this->adminToken)
        );
    }

    public function testEmptyCourseIds(): void
    {
        $this->client->jsonRequest(
            'PUT',
            '/v1/admin/testing/tests/' . RequestFixture::TEST_ID . '/update',
            [
                'courseIds' => [],
            ],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(422, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'courseIds' => 'This collection should contain 1 element or more.',
        ]], $data);
    }

    public function testInvalidCourseIds(): void
    {
        $this->client->jsonRequest(
            'PUT',
            '/v1/admin/testing/tests/' . RequestFixture::TEST_ID . '/update',
            [
                'courseIds' => ['invalid'],
            ],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(422, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'courseIds[0]' => 'This is not a valid UUID.',
        ]], $data);
    }
}
