<?php

declare(strict_types=1);

namespace Tests\Functional\Testing\Attempt\Finish;

use App\Testing\Entity\Attempt\AttemptId;
use App\Testing\Entity\Attempt\AttemptRepository;
use App\Testing\Entity\Attempt\Status;
use Doctrine\ORM\EntityManagerInterface;
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
    private readonly AttemptRepository $attempts;
    private string $userToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
        $this->client->disableReboot();

        $this->container = $this->client->getContainer();

        $fixturesLoader = new FixturesLoader($this->container);
        $fixturesLoader->loadFixtures([RequestFixture::class]);

        /** @var EntityManagerInterface $em */
        $em = $this->container->get(EntityManagerInterface::class);
        $this->attempts = new AttemptRepository($em);

        $this->userToken = $this->getAccessToken(
            $this->client,
            RequestFixture::USER_EMAIL,
            RequestFixture::USER_PASSWORD,
        );
    }

    public function testAnonymousUserReturns403(): void
    {
        $this->client->jsonRequest('PUT', '/v1/testing/attempts/' . RequestFixture::ATTEMPT_ID . '/finish');

        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testSuccessGuestUser(): void
    {
        $this->client->jsonRequest(
            'PUT',
            '/v1/testing/attempts/' . RequestFixture::ATTEMPT_ID . '/finish',
            [],
            ['HTTP_X-Guest-ID' => RequestFixture::GUEST_ID]
        );

        self::assertEquals(204, $this->client->getResponse()->getStatusCode());

        $attempt = $this->attempts->get(new AttemptId(RequestFixture::ATTEMPT_ID));
        self::assertEquals(Status::passed(), $attempt->getStatus());
    }

    public function testSuccess(): void
    {
        $this->client->catchExceptions(false);
        $this->client->jsonRequest(
            'PUT',
            '/v1/testing/attempts/' . RequestFixture::ATTEMPT_ID . '/finish',
            [],
            $this->authHeaders($this->userToken)
        );
        self::assertEquals(204, $this->client->getResponse()->getStatusCode());

        $attempt = $this->attempts->get(new AttemptId(RequestFixture::ATTEMPT_ID));
        self::assertEquals(Status::passed(), $attempt->getStatus());
    }

    public function testNotFound(): void
    {
        $this->client->jsonRequest(
            'PUT',
            '/v1/testing/attempts/' . RequestFixture::ATTEMPT_ID_NOT_FOUND . '/finish',
            [],
            $this->authHeaders($this->userToken)
        );

        self::assertEquals(409, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['message' => 'Attempt not found.'], $data);
    }
}
