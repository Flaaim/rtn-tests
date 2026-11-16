<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Profile\Add;

use App\Profile\Event\ProfileAdded;
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
    private string $adminToken;
    private string $userToken;

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
    }

    public function testUnauthenticatedReturns401(): void
    {
        $this->client->jsonRequest('POST', '/v1/admin/profiles');

        self::assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testForbiddenForRegularUsers(): void
    {
        $this->client->jsonRequest(
            'POST',
            '/v1/admin/profiles',
            [],
            $this->authHeaders($this->userToken)
        );

        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testSuccess(): void
    {
        /** @var InMemoryTransport $transport */
        $transport = $this->client->getContainer()->get('messenger.transport.async_outbox');
        $transport->reset();

        $this->client->jsonRequest(
            'POST',
            '/v1/admin/profiles',
            [
                'email' => 'new@email.ru',
                'role' => 'user',
                'name' => 'name',
                'surname' => 'surname',
            ],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(201, $this->client->getResponse()->getStatusCode());

        self::assertCount(1, $transport->getSent());

        $message = $transport->getSent()[0]->getMessage();

        self::assertInstanceOf(ProfileAdded::class, $message);

        self::assertNotNull($message->id);
        self::assertEquals('new@email.ru', $message->email);
    }

    public function testEmpty(): void
    {
        $this->client->jsonRequest(
            'POST',
            '/v1/admin/profiles',
            [],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(422, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'email' => 'This value should not be blank.',
            'role' => 'The value you selected is not a valid choice.',
        ]], $data);
    }
}
