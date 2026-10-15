<?php

declare(strict_types=1);

namespace Tests\Functional\Auth\JoinByMagicLink;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\UserRepository;
use App\Auth\Event\MagicLinkRequested;
use App\Auth\Event\UserCreated;
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
final class RequestActionTest extends WebTestCase
{
    private readonly KernelBrowser $client;
    private readonly UserRepository $users;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
        $this->client->disableReboot();

        $container = $this->client->getContainer();

        $fixture = new FixturesLoader($container);
        $fixture->loadFixtures([RequestFixture::class]);

        /** @var EntityManagerInterface $em */
        $em = $container->get(EntityManagerInterface::class);
        $this->users = new UserRepository($em);
    }

    public function testSuccess(): void
    {
        /** @var InMemoryTransport $transport */
        $transport = $this->client->getContainer()->get('messenger.transport.async');
        $transport->reset();

        $this->client->jsonRequest(
            'POST',
            '/v1/auth/join/magic/request',
            [
                'email' => RequestFixture::USER_EMAIL,
            ]
        );

        self::assertEquals(201, $this->client->getResponse()->getStatusCode());

        $user = $this->users->getByEmail(new Email(RequestFixture::USER_EMAIL));

        self::assertEquals(RequestFixture::USER_EMAIL, $user->getEmail()->getValue());

        self::assertCount(2, $transport->getSent());

        $userCreatedMessages = array_filter($transport->getSent(), static fn ($message) => $message->getMessage() instanceof UserCreated);
        $userCreatedMessages = array_values($userCreatedMessages);

        $userCreatedMessage = $userCreatedMessages[0]->getMessage();
        self::assertNotNull($userCreatedMessage->id);
        self::assertEquals(RequestFixture::USER_EMAIL, $userCreatedMessage->email);
        self::assertEquals('user', $userCreatedMessage->role);

        $magicLinkRequestedMessages = array_filter($transport->getSent(), static fn ($message) => $message->getMessage() instanceof MagicLinkRequested);
        $magicLinkRequestedMessages = array_values($magicLinkRequestedMessages);

        $magicLinkRequestedMessage = $magicLinkRequestedMessages[0]->getMessage();
        self::assertEquals(RequestFixture::USER_EMAIL, $magicLinkRequestedMessage->email);
        self::assertNotNull($magicLinkRequestedMessage->token);
    }

    public function testAlreadyExists(): void
    {
        $transport = $this->client->getContainer()->get('messenger.transport.async');
        $transport->reset();
        $this->client->jsonRequest(
            'POST',
            '/v1/auth/join/magic/request',
            [
                'email' => RequestFixture::EXISTS_EMAIL,
            ]
        );

        self::assertEquals(201, $this->client->getResponse()->getStatusCode());
        $user = $this->users->getByEmail(new Email(RequestFixture::EXISTS_EMAIL));

        self::assertEquals(RequestFixture::EXISTS_EMAIL, $user->getEmail()->getValue());
        self::assertCount(1, $transport->getSent());

        $message = $transport->getSent()[0]->getMessage();
        self::assertInstanceOf(MagicLinkRequested::class, $message);
        self::assertEquals(RequestFixture::EXISTS_EMAIL, $message->email);
        self::assertNotNull($message->token);
    }

    public function testInvalid(): void
    {
        $this->client->jsonRequest(
            'POST',
            '/v1/auth/join/magic/request',
            [
                'email' => 'invalid',
            ]
        );

        self::assertEquals(422, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'email' => 'This value is not a valid email address.',
        ]], $data);
    }
}
