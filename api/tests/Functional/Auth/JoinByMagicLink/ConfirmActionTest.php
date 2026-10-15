<?php

declare(strict_types=1);

namespace Tests\Functional\Auth\JoinByMagicLink;

use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\UserRepository;
use App\Auth\Event\GuestJoined;
use App\Auth\Event\UserJoinConfirmed;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;
use Tests\Functional\FixturesLoader;
use Tests\Functional\Json;

/**
 * @internal
 * @coversNothing
 */
final class ConfirmActionTest extends WebTestCase
{
    private KernelBrowser $client;
    private UserRepository $users;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
        $this->client->disableReboot();

        $container = $this->client->getContainer();

        $fixturesLoader = new FixturesLoader($container);
        $fixturesLoader->loadFixtures([ConfirmFixture::class]);

        /** @var EntityManagerInterface $em */
        $em = $container->get(EntityManagerInterface::class);
        $this->users = new UserRepository($em);
    }

    public function testSuccess(): void
    {
        /** @var InMemoryTransport $transport */
        $transport = $this->client->getContainer()->get('messenger.transport.async');
        $transport->reset();

        $this->client->jsonRequest('POST', '/v1/auth/join/magic/confirm', [
            'token' => ConfirmFixture::USER_TOKEN,
        ]);

        self::assertEquals(201, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertArrayHasKey('access_token', $data);
        self::assertArrayHasKey('refresh_token', $data);
        self::assertArrayHasKey('expires_in', $data);

        $user = $this->users->get(new Id(ConfirmFixture::USER_ID));
        self::assertTrue($user->isActive());
        self::assertNull($user->getMagicLinkToken());

        self::assertCount(1, $transport->getSent());

        $message = $transport->getSent()[0]->getMessage();

        self::assertInstanceOf(UserJoinConfirmed::class, $message);
    }

    public function testSuccessGuest(): void
    {
        /** @var InMemoryTransport $transport */
        $transport = $this->client->getContainer()->get('messenger.transport.async');
        $transport->reset();

        $this->client->jsonRequest(
            'POST',
            '/v1/auth/join/magic/confirm',
            [
                'token' => ConfirmFixture::GUEST_TOKEN,
            ],
            ['HTTP_X-Guest-ID' => ConfirmFixture::GUEST_ID]
        );

        self::assertEquals(201, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertArrayHasKey('access_token', $data);
        self::assertArrayHasKey('refresh_token', $data);
        self::assertArrayHasKey('expires_in', $data);

        $user = $this->users->get(new Id(ConfirmFixture::GUEST_ID));
        self::assertTrue($user->isActive());
        self::assertNull($user->getMagicLinkToken());

        self::assertCount(2, $transport->getSent());

        $userCreatedMessages = array_filter($transport->getSent(), static fn ($message) => $message->getMessage() instanceof UserJoinConfirmed);
        $userCreatedMessages = array_values($userCreatedMessages);
        $userCreateMessage = $userCreatedMessages[0]->getMessage();

        self::assertNotNull($userCreateMessage->email);

        $guestJoinedMessages = array_filter($transport->getSent(), static fn ($message) => $message->getMessage() instanceof GuestJoined);
        $guestJoinedMessages = array_values($guestJoinedMessages);
        $guestJoinedMessage = $guestJoinedMessages[0]->getMessage();
        self::assertEquals(ConfirmFixture::GUEST_ID, $guestJoinedMessage->guestId);
        self::assertEquals(ConfirmFixture::GUEST_ID, $guestJoinedMessage->userId);
    }

    public function testExpired(): void
    {
        $this->client->jsonRequest('POST', '/v1/auth/join/magic/confirm', [
            'token' => ConfirmFixture::EXPIRED_TOKEN,
        ]);

        self::assertEquals(409, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['message' => 'Token is expired.'], $data);
    }

    public function testNotFound(): void
    {
        $this->client->jsonRequest('POST', '/v1/auth/join/magic/confirm', [
            'token' => ConfirmFixture::NOT_FOUND_TOKEN,
        ]);

        self::assertEquals(409, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['message' => 'Incorrect magicLinkToken.'], $data);
    }

    public function testInvalid(): void
    {
        $this->client->jsonRequest('POST', '/v1/auth/join/magic/confirm', [
            'token' => 'invalid',
        ]);
        self::assertEquals(422, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'token' => 'This is not a valid UUID.',
        ]], $data);
    }
}
