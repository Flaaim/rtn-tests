<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Tasks\Delete;

use App\Parser\Query\Task\TaskFetcherInterface;
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
    private KernelBrowser $client;

    private readonly TaskFetcherInterface $fetcher;
    private readonly string $adminToken;
    private readonly string $userToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();

        $container = $this->client->getContainer();
        $fixturesLoader = new FixturesLoader($container);
        $fixturesLoader->loadFixtures([RequestFixture::class]);

        /** @var TaskFetcherInterface $fetcher */
        $this->fetcher = $container->get(TaskFetcherInterface::class);

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

    public function testUnauthenticatedReturn401(): void
    {
        $queryString = http_build_query([
            'ids' => [
                RequestFixture::TASK_ONE_ID,
                RequestFixture::TASK_TWO_ID,
            ],
        ]);
        $this->client->jsonRequest('DELETE', '/v1/admin/tasks?' . $queryString);

        self::assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testForbiddenForRegularUser(): void
    {
        $queryString = http_build_query([
            'ids' => [
                RequestFixture::TASK_ONE_ID,
                RequestFixture::TASK_TWO_ID,
            ],
        ]);
        $this->client->jsonRequest(
            'DELETE',
            '/v1/admin/tasks?' . $queryString,
            [],
            $this->authHeaders($this->userToken)
        );

        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testSuccess(): void
    {
        $queryString = http_build_query([
            'ids' => [
                RequestFixture::TASK_ONE_ID,
                RequestFixture::TASK_TWO_ID,
            ],
        ]);

        $this->client->jsonRequest(
            'DELETE',
            '/v1/admin/tasks?' . $queryString,
            [],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(204, $this->client->getResponse()->getStatusCode());

        $tasks = $this->fetcher->findAll();
        self::assertEmpty($tasks);
    }

    public function testEmptyIds(): void
    {
        $queryString = http_build_query([]);

        $this->client->jsonRequest(
            'DELETE',
            '/v1/admin/tasks?' . $queryString,
            [],
            $this->authHeaders($this->adminToken)
        );
        self::assertEquals(422, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'ids' => 'This value should not be blank.',
        ]], $data);
    }

    public function testInvalidIds(): void
    {
        $queryString = http_build_query([
            'ids' => ['one', 'two'],
        ]);
        $this->client->jsonRequest(
            'DELETE',
            '/v1/admin/tasks?' . $queryString,
            [],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(422, $this->client->getResponse()->getStatusCode());
        self::assertJson($body = $this->client->getResponse()->getContent());
        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'ids[0]' => 'This is not a valid UUID.',
            'ids[1]' => 'This is not a valid UUID.',
        ]], $data);
    }
}
