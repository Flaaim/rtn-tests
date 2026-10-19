<?php

declare(strict_types=1);

namespace Tests\Functional\Testing\Attempt\LaunchMistakes;

use App\Testing\Event\Attempt\TimeoutAttemptCommand;
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
            UserFixture::USER_EMAIL,
            UserFixture::USER_PASSWORD,
        );
    }

    public function testUnauthenticatedReturns401(): void
    {
        $this->client->jsonRequest('POST', '/v1/testing/attempts/mistakes');

        self::assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testSuccess(): void
    {
        /** @var InMemoryTransport $transport */
        $transport = $this->client->getContainer()->get('messenger.transport.async');
        $transport->reset();
        $this->client->jsonRequest(
            'POST',
            '/v1/testing/attempts/mistakes',
            [
                'testId' => RequestFixture::TEST_ID,
            ],
            $this->authHeaders($this->userToken)
        );
        self::assertEquals(201, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertArrayHasKey('attemptId', $data);

        self::assertCount(1, $transport->getSent());

        $message = $transport->getSent()[0]->getMessage();
        self::assertInstanceOf(TimeoutAttemptCommand::class, $message);
        self::assertNotNull($message->attemptId);
    }

    public function testNotFound(): void
    {
        $this->client->jsonRequest(
            'POST',
            '/v1/testing/attempts/mistakes',
            [
                'testId' => RequestFixture::TEST_NOT_FOUND,
            ],
            $this->authHeaders($this->userToken)
        );

        self::assertEquals(409, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['message' => 'У вас пока нет ошибок для проработки. Отличная работа!'], $data);
    }

    public function testEmpty(): void
    {
        $this->client->jsonRequest(
            'POST',
            '/v1/testing/attempts/mistakes',
            [],
            $this->authHeaders($this->userToken)
        );
        self::assertEquals(422, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());
        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'testId' => 'This value should not be blank.',
        ]], $data);
    }

    public function testInvalid(): void
    {
        $this->client->jsonRequest(
            'POST',
            '/v1/testing/attempts/mistakes',
            [
                'testId' => 'invalid',
            ],
            $this->authHeaders($this->userToken)
        );

        self::assertEquals(422, $this->client->getResponse()->getStatusCode());
        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'testId' => 'This is not a valid UUID.',
        ]], $data);
    }
}
