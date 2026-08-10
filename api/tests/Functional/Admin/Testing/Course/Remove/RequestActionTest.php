<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Testing\Course\Remove;

use App\SharedDomain\Filesystem\InMemoryFileSystemPath;
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

    private InMemoryFileSystemPath $fileSystemPath;
    private string $adminToken;
    private string $userToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
        $this->container = $this->client->getContainer();

        $this->fileSystemPath = InMemoryFileSystemPath::createReal();

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
        $this->client->jsonRequest('DELETE', '/v1/admin/testing/courses/' . RequestFixture::COURSE_ID);

        self::assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testForbiddenForRegularUsers(): void
    {
        $this->client->jsonRequest(
            'DELETE',
            '/v1/admin/testing/courses/' . RequestFixture::COURSE_ID,
            [],
            $this->authHeaders($this->userToken)
        );

        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testSuccess(): void
    {
        $directory = $this->fileSystemPath->getValue() . \DIRECTORY_SEPARATOR . RequestFixture::COURSE_ID;
        $this->createDirectory($directory);

        self::assertDirectoryExists($directory);

        $this->client->jsonRequest(
            'DELETE',
            '/v1/admin/testing/courses/' . RequestFixture::COURSE_ID,
            [],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(204, $this->client->getResponse()->getStatusCode());
    }

    public function testNotFound(): void
    {
        $this->client->jsonRequest(
            'DELETE',
            '/v1/admin/testing/courses/' . RequestFixture::COURSE_NOT_FOUND_ID,
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
            'DELETE',
            '/v1/admin/testing/courses/invalid',
            [],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(422, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'id' => 'This is not a valid UUID.',
        ]], $data);
    }

    private function createDirectory(string $path): void
    {
        mkdir($path, 0o777, true);
    }
}
