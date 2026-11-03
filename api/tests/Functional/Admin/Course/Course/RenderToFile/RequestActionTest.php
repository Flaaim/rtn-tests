<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Course\Course\RenderToFile;

use App\Course\Event\CourseRendered;
use App\SharedDomain\Filesystem\InMemoryFileSystemPath;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
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
    private readonly string $adminToken;
    private readonly string $userToken;
    private readonly InMemoryFileSystemPath $fileSystemPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
        $this->client->disableReboot();

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

        $this->fileSystemPath = InMemoryFileSystemPath::createReal();
    }

    public function testUnauthenticatedReturns401(): void
    {
        $this->client->jsonRequest(
            'POST',
            '/v1/admin/testing/courses/' . RequestFixture::COURSE_ID . '/render'
        );

        self::assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testForbiddenForRegularUsers(): void
    {
        $this->client->jsonRequest(
            'POST',
            '/v1/admin/testing/courses/' . RequestFixture::COURSE_ID . '/render',
            [],
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
            'POST',
            '/v1/admin/testing/courses/' . RequestFixture::COURSE_ID . '/render',
            [
                'amount' => '450.00',
            ],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(204, $this->client->getResponse()->getStatusCode());

        self::assertFileExists(
            $this->fileSystemPath->getValue() .
                \DIRECTORY_SEPARATOR . RequestFixture::COURSE_ID .
                \DIRECTORY_SEPARATOR . RequestFixture::COURSE_ID . '.pdf'
        );

        $message = $transport->getSent()[0]->getMessage();
        self::assertInstanceOf(CourseRendered::class, $message);
        $expectedFilePath = RequestFixture::COURSE_ID . \DIRECTORY_SEPARATOR . RequestFixture::COURSE_ID . '.pdf';

        self::assertEquals('450.00', $message->amount);
        self::assertEquals(RequestFixture::COURSE_ID, $message->courseId);
        self::assertEquals($expectedFilePath, $message->filePath);
        self::assertEquals(RequestFixture::COURSE_CIPHER, $message->cipher);
    }

    public function testInvalid(): void
    {
        $this->client->jsonRequest(
            'POST',
            '/v1/admin/testing/courses/invalid/render',
            [
                'amount' => '450.00',
            ],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(422, $this->client->getResponse()->getStatusCode());
    }

    public function testNotFound(): void
    {
        $this->client->jsonRequest(
            'POST',
            '/v1/admin/testing/courses/' . RequestFixture::COURSE_NOT_FOUND_ID . '/render',
            [
                'amount' => '450.00',
            ],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(409, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['message' => 'Course not found.'], $data);
    }
}
