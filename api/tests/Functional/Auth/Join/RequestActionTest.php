<?php

declare(strict_types=1);

namespace Tests\Functional\Auth\Join;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\UserRepository;
use App\Auth\Event\GuestJoined;
use App\Auth\Event\JoinByEmailRequested;
use App\Auth\Event\UserCreated;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\Functional\FixturesLoader;
use Tests\Functional\Json;

/**
 * @internal
 * @coversNothing
 */
final class RequestActionTest extends WebTestCase
{
    private KernelBrowser $client;
    private UserRepository $users;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
        $container = $this->client->getContainer();

        $fixturesLoader = new FixturesLoader($container);
        $fixturesLoader->loadFixtures([RequestFixture::class]);

        /** @var EntityManagerInterface $em */
        $em = $container->get(EntityManagerInterface::class);
        $this->users = new UserRepository($em);
    }

    public function testUserAlreadyExists(): void
    {
        $this->client->jsonRequest('POST', '/v1/auth/join/request', [
            'email' => 'exists@email.com',
            'password' => 'password',
        ]);

        self::assertEquals(409, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['message' => 'User already exists.'], $data);
    }

    public function testSuccess(): void
    {
        $email = 'new@email.com';
        $password = 'password';
        $transport = $this->client->getContainer()->get('messenger.transport.async');
        $transport->reset();

        $this->client->jsonRequest('POST', '/v1/auth/join/request', [
            'email' => $email,
            'password' => $password,
        ]);

        self::assertEquals(201, $this->client->getResponse()->getStatusCode());
        $user = $this->users->getByEmail(new Email($email));

        self::assertEquals($user->getEmail()->getValue(), $email);

        self::assertCount(2, $transport->getSent());

        $joinMessages = array_filter($transport->getSent(), static fn ($message) => $message->getMessage() instanceof JoinByEmailRequested);

        $joinMessages = array_values($joinMessages);
        $joinMessage = $joinMessages[0]->getMessage();

        self::assertEquals($email, $joinMessage->email);
        self::assertNotEmpty($joinMessage->token);

        $createMessages = array_filter($transport->getSent(), static fn ($message) => $message->getMessage() instanceof UserCreated);

        $createMessages = array_values($createMessages);
        $createMessage = $createMessages[0]->getMessage();

        self::assertEquals($email, $createMessage->email);
        self::assertNotEmpty($createMessage->id);
        self::assertNotEmpty($createMessage->role);
    }

    public function testSuccessGuest(): void
    {
        $email = 'new@email.com';
        $password = 'password';
        $guestId = 'e92fa0c3-beb1-4fa6-bd85-fcc6a8dba182';

        $transport = $this->client->getContainer()->get('messenger.transport.async');
        $transport->reset();

        $this->client->jsonRequest(
            'POST',
            '/v1/auth/join/request',
            [
                'email' => $email,
                'password' => $password,
            ],
            ['HTTP_X-Guest-ID' => $guestId]
        );

        self::assertEquals(201, $this->client->getResponse()->getStatusCode());

        self::assertCount(3, $transport->getSent());

        $guestJoinedMessages = array_filter($transport->getSent(), static fn ($message) => $message->getMessage() instanceof GuestJoined);
        $guestJoinedMessages = array_values($guestJoinedMessages);
        $guestJoinedMessage = $guestJoinedMessages[0]->getMessage();

        /** @var GuestJoined $guestJoinedMessage */
        self::assertEquals($guestId, $guestJoinedMessage->guestId);
        self::assertNotNull($guestJoinedMessage->userId);
    }

    public function testInvalidCredentials(): void
    {
        $this->client->jsonRequest('POST', '/v1/auth/join/request', [
            'email' => 'some_text',
            'password' => '',
        ]);

        self::assertEquals(422, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());
        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'email' => 'This value is not a valid email address.',
            'password' => 'This value is too short. It should have 6 characters or more.',
        ]], $data);
    }

    public function testInvalidGuestId(): void
    {
        $email = 'new@email.com';
        $password = 'password';
        $this->client->jsonRequest(
            'POST',
            '/v1/auth/join/request',
            [
                'email' => $email,
                'password' => $password,
            ],
            ['HTTP_X-Guest-ID' => 'some_text']
        );

        self::assertEquals(422, $this->client->getResponse()->getStatusCode());
        self::assertJson($body = $this->client->getResponse()->getContent());
        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'guestId' => 'This is not a valid UUID.',
        ]], $data);
    }
}
