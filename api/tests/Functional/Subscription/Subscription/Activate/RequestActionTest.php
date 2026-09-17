<?php

declare(strict_types=1);

namespace Tests\Functional\Subscription\Subscription\Activate;

use App\Subscription\Event\Subscription\SubscriptionPurchased;
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
    private string $userToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
        $this->client->disableReboot();

        $this->container = $this->client->getContainer();

        $fixturesLoader = new FixturesLoader($this->container);
        $fixturesLoader->loadFixtures([RequestFixture::class]);

        $this->userToken = $this->getAccessToken(
            $this->client,
            RequestFixture::USER_EMAIL,
            RequestFixture::USER_PASSWORD,
        );
    }

    public function testUnauthenticatedReturns401(): void
    {
        $this->client->jsonRequest('POST', '/v1/subscriptions');

        self::assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testSuccess(): void
    {
        /** @var InMemoryTransport $transport */
        $transport = $this->client->getContainer()->get('messenger.transport.async');
        $transport->reset();

        $this->client->jsonRequest(
            'POST',
            '/v1/subscriptions',
            [
                'durationDays' => 5,
                'plan' => 'basic',
            ],
            $this->authHeaders($this->userToken),
        );

        self::assertEquals(201, $this->client->getResponse()->getStatusCode());

        self::assertCount(1, $transport->getSent());

        $message = $transport->getSent()[0]->getMessage();
        self::assertInstanceOf(SubscriptionPurchased::class, $message);
    }

    public function testEmpty(): void
    {
        $this->client->jsonRequest(
            'POST',
            '/v1/subscriptions',
            [],
            $this->authHeaders($this->userToken),
        );

        self::assertEquals(422, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'durationDays' => 'This value should be greater than 0.',
            'plan' => 'The value you selected is not a valid choice.',
        ]], $data);
    }

    public function testInvalid(): void
    {
        $this->client->jsonRequest(
            'POST',
            '/v1/subscriptions',
            [
                'durationDays' => 0,
                'plan' => 'test',
            ],
            $this->authHeaders($this->userToken),
        );
        self::assertEquals(422, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'durationDays' => 'This value should be greater than 0.',
            'plan' => 'The value you selected is not a valid choice.',
        ]], $data);
    }
}
