<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Testing\Test\Activate;

use App\Testing\Entity\Test\TestId;
use App\Testing\Entity\Test\TestRepository;
use App\Testing\Event\TestActivated;
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
    private readonly TestRepository $tests;
    private readonly ContainerInterface $container;
    private string $adminToken;
    private string $userToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
        $this->client->disableReboot();

        $this->container = $this->client->getContainer();
        /** @var EntityManagerInterface $em */
        $em = $this->container->get(EntityManagerInterface::class);
        $this->tests = new TestRepository($em);

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
        $this->client->jsonRequest('PUT', '/v1/admin/testing/tests/' . RequestFixture::TEST_ID . '/activate');

        self::assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testForbiddenForRegularUsers(): void
    {
        $this->client->jsonRequest(
            'PUT',
            '/v1/admin/testing/tests/' . RequestFixture::TEST_ID . '/activate',
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
            'PUT',
            '/v1/admin/testing/tests/' . RequestFixture::TEST_ID . '/activate',
            [],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(204, $this->client->getResponse()->getStatusCode());

        self::assertCount(1, $transport->getSent());

        $message = $transport->getSent()[0]->getMessage();

        self::assertInstanceOf(TestActivated::class, $message);

        self::assertEquals(RequestFixture::TEST_ID, $message->id);

        $test = $this->tests->get(new TestId(RequestFixture::TEST_ID));

        self::assertTrue($test->isActive());
    }

    public function testActivateAlready(): void
    {
        $this->client->jsonRequest(
            'PUT',
            '/v1/admin/testing/tests/' . RequestFixture::TEST_ACTIVE_ID . '/activate',
            [],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(409, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals([
            'message' => 'Test is already active.',
        ], $data);
    }

    public function testNotFound(): void
    {
        $this->client->jsonRequest(
            'PUT',
            '/v1/admin/testing/tests/' . RequestFixture::TEST_NOT_FOUND_ID . '/activate',
            [],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(409, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals([
            'message' => 'Test not found.',
        ], $data);
    }

    public function testInvalid(): void
    {
        $this->client->jsonRequest(
            'PUT',
            '/v1/admin/testing/tests/invalid/activate',
            [],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(422, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals([
            'errors' => [
                'id' => 'This is not a valid UUID.',
            ],
        ], $data);
    }
}
