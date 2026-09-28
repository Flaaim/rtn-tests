<?php

declare(strict_types=1);

namespace Tests\Functional\Testing\Attempt\Launch;

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
    private string $activeUserToken; // Пользователь с базовой подпиской
    private string $trialUsedUserToken;
    private string $newUserToken;

    private string $expiredUserToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
        $this->client->disableReboot();

        $this->container = $this->client->getContainer();

        $fixturesLoader = new FixturesLoader($this->container);
        $fixturesLoader->loadFixtures([RequestFixture::class]);

        $this->activeUserToken = $this->getAccessToken(
            $this->client,
            UserFixture::USER_EMAIL,
            UserFixture::USER_PASSWORD,
        );

        $this->trialUsedUserToken = $this->getAccessToken(
            $this->client,
            UserFixture::TRIAL_USER_EMAIL,
            UserFixture::USER_PASSWORD,
        );

        $this->newUserToken = $this->getAccessToken(
            $this->client,
            UserFixture::NEW_USER_EMAIL,
            UserFixture::USER_PASSWORD,
        );

        $this->expiredUserToken = $this->getAccessToken(
            $this->client,
            UserFixture::EXPIRED_USER_EMAIL,
            UserFixture::USER_PASSWORD,
        );
    }

    public function testUnauthenticatedReturns401(): void
    {
        $this->client->jsonRequest('POST', '/v1/testing/attempts');

        self::assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testSuccess(): void
    {
        /** @var InMemoryTransport $transport */
        $transport = $this->client->getContainer()->get('messenger.transport.async');
        $transport->reset();

        $this->client->jsonRequest(
            'POST',
            '/v1/testing/attempts',
            [
                'testId' => RequestFixture::TEST_ID,
                'ticketNumber' => RequestFixture::TICKET_NUMBER,
            ],
            $this->authHeaders($this->activeUserToken)
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

    public function testTrialUsed(): void
    {
        $transport = $this->client->getContainer()->get('messenger.transport.async');
        $transport->reset();

        $this->client->jsonRequest(
            'POST',
            '/v1/testing/attempts',
            [
                'testId' => RequestFixture::TEST_ID,
                'ticketNumber' => RequestFixture::TICKET_NUMBER,
            ],
            $this->authHeaders($this->trialUsedUserToken)
        );

        self::assertEquals(409, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['message' => 'Ваш пробный период завершен. Для продолжения необходимо приобрести подписку.'], $data);
    }

    public function testNewUserLaunch(): void
    {
        $transport = $this->client->getContainer()->get('messenger.transport.async');
        $transport->reset();

        $this->client->jsonRequest(
            'POST',
            '/v1/testing/attempts',
            [
                'testId' => RequestFixture::TEST_ID,
                'ticketNumber' => RequestFixture::TICKET_NUMBER,
            ],
            $this->authHeaders($this->newUserToken)
        );

        self::assertEquals(201, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertArrayHasKey('attemptId', $data);

        self::assertCount(2, $transport->getSent());
    }

    public function testExpiredLaunch(): void
    {
        $transport = $this->client->getContainer()->get('messenger.transport.async');
        $transport->reset();

        $this->client->jsonRequest(
            'POST',
            '/v1/testing/attempts',
            [
                'testId' => RequestFixture::TEST_ID,
                'ticketNumber' => RequestFixture::TICKET_NUMBER,
            ],
            $this->authHeaders($this->expiredUserToken)
        );
        self::assertEquals(409, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['message' => 'Ваш оплаченный период завершен. Для продолжения необходимо приобрести подписку.'], $data);
    }

    public function testNotFound(): void
    {
        $this->client->jsonRequest(
            'POST',
            '/v1/testing/attempts',
            [
                'testId' => RequestFixture::TEST_NOT_FOUND,
                'ticketNumber' => RequestFixture::TICKET_NUMBER,
            ],
            $this->authHeaders($this->activeUserToken)
        );

        self::assertEquals(409, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals([
            'message' => 'Test not found.',
        ], $data);
    }

    public function testTicketNumberNotFound(): void
    {
        $this->client->jsonRequest(
            'POST',
            '/v1/testing/attempts',
            [
                'testId' => RequestFixture::TEST_ID,
                'ticketNumber' => 6,
            ],
            $this->authHeaders($this->activeUserToken)
        );

        self::assertEquals(409, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals([
            'message' => 'Invalid ticket number.',
        ], $data);
    }

    public function testInvalid(): void
    {
        $this->client->jsonRequest(
            'POST',
            '/v1/testing/attempts',
            [
                'testId' => 'invalid',
                'ticketNumber' => 'invalid',
            ],
            $this->authHeaders($this->activeUserToken)
        );
        self::assertEquals(422, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'testId' => 'This is not a valid UUID.',
            'ticketNumber' => 'This value should be greater than 0.',
        ]], $data);
    }
}
