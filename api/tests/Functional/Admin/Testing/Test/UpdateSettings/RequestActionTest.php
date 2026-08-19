<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Testing\Test\UpdateSettings;

use App\Testing\Entity\Test\TestId;
use App\Testing\Entity\Test\TestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
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

    private readonly TestRepository $tests;

    private string $adminToken;
    private string $userToken;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        $this->container = $this->client->getContainer();

        $fixturesLoader = new FixturesLoader($this->container);
        $fixturesLoader->loadFixtures([RequestFixture::class]);

        /** @var EntityManagerInterface $em */
        $em = $this->container->get(EntityManagerInterface::class);
        $this->tests = new TestRepository($em);

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
        $this->client->jsonRequest('PUT', '/v1/admin/testing/tests/' . RequestFixture::TEST_ID . '/update-settings');

        self::assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testForbiddenForRegularUsers(): void
    {
        $this->client->jsonRequest(
            'PUT',
            '/v1/admin/testing/tests/' . RequestFixture::TEST_ID . '/update-settings',
            [],
            $this->authHeaders($this->userToken)
        );

        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testSuccess(): void
    {
        $this->client->jsonRequest(
            'PUT',
            '/v1/admin/testing/tests/' . RequestFixture::TEST_ID . '/update-settings',
            [
                'numberOfTickets' => 5,
                'numberQuestionsInTicket' => 2,
                'allowedMistakes' => 1,
            ],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(204, $this->client->getResponse()->getStatusCode());

        $test = $this->tests->get(new TestId(RequestFixture::TEST_ID));

        self::assertEquals(5, $test->getSettings()->getNumberOfTickets());
        self::assertEquals(2, $test->getSettings()->getNumberQuestionsInTicket());
        self::assertEquals(1, $test->getSettings()->getAllowedMistakes());

        self::assertCount(5, $test->getTickets());
    }

    public function testNotFound(): void
    {
        $this->client->jsonRequest(
            'PUT',
            '/v1/admin/testing/tests/' . RequestFixture::TEST_NOT_FOUND_ID . '/update-settings',
            [
                'numberOfTickets' => 5,
                'numberQuestionsInTicket' => 2,
                'allowedMistakes' => 1,
            ],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(409, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['message' => 'Test not found.'], $data);
    }

    public function testUpdateSettingsActive(): void
    {
        $this->client->jsonRequest(
            'PUT',
            '/v1/admin/testing/tests/' . RequestFixture::TEST_ACTIVE_ID . '/update-settings',
            [
                'numberOfTickets' => 5,
                'numberQuestionsInTicket' => 2,
                'allowedMistakes' => 1,
            ],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(409, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['message' => 'Cannot change settings of an active test.'], $data);
    }

    public function testInvalidSettings(): void
    {
        $this->client->jsonRequest(
            'PUT',
            '/v1/admin/testing/tests/' . RequestFixture::TEST_ID . '/update-settings',
            [
                'numberOfTickets' => 0,
                'numberQuestionsInTicket' => 0,
                'allowedMistakes' => 0,
            ],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(422, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'numberOfTickets' => 'This value should be greater than 0.',
            'numberQuestionsInTicket' => 'This value should be greater than 0.',
            'allowedMistakes' => 'This value should be greater than 0.',
        ]], $data);
    }
}
