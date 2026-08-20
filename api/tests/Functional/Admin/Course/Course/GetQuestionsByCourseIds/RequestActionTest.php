<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Course\Course\GetQuestionsByCourseIds;

use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
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
    private string $adminToken;
    private string $userToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
        $this->container = $this->client->getContainer();

        $fixturesLoader = new FixturesLoader($this->container);
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
        $queryString = http_build_query([
            'ids' => [
                RequestFixture::COURSE_ID,
            ],
        ]);
        $this->client->jsonRequest('GET', '/v1/admin/testing/courses/questions?' . $queryString);

        self::assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testForbiddenForRegularUsers(): void
    {
        $queryString = http_build_query([
            'ids' => [
                RequestFixture::COURSE_ID,
            ],
        ]);
        $this->client->jsonRequest(
            'GET',
            '/v1/admin/testing/courses/questions?' . $queryString,
            [],
            $this->authHeaders($this->userToken)
        );

        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testSuccess(): void
    {
        $queryString = http_build_query([
            'ids' => [
                RequestFixture::COURSE_ID,
                RequestFixture::COURSE_ANOTHER_ID,
            ],
        ]);
        $this->client->catchExceptions(false);
        $this->client->jsonRequest(
            'GET',
            '/v1/admin/testing/courses/questions?' . $queryString,
            [],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertCount(4, $data);

        $question = $data[0];

        self::assertArrayHasKey('id', $question);
        self::assertArrayHasKey('text', $question);
        self::assertArrayHasKey('questionImg', $question);
        self::assertArrayHasKey('answers', $question);
        self::assertArrayHasKey('form', $question);
    }

    public function testEmpty(): void
    {
        $queryString = http_build_query([
            'ids' => [
                RequestFixture::COURSE_NOT_FOUND_ID,
            ],
        ]);
        $this->client->jsonRequest(
            'GET',
            '/v1/admin/testing/courses/questions?' . $queryString,
            [],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(409, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['message' => 'No questions found.'], $data);
    }

    public function testInvalidId(): void
    {
        $queryString = http_build_query([
            'ids' => [
                'invalid-id',
            ],
        ]);
        $this->client->jsonRequest(
            'GET',
            '/v1/admin/testing/courses/questions?' . $queryString,
            [],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(422, $this->client->getResponse()->getStatusCode());
    }
}
