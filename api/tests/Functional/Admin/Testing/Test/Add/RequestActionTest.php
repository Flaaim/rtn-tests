<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Testing\Test\Add;

use App\Testing\Entity\Test\TestRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    private readonly TestRepository $tests;
    private string $adminToken;
    private string $userToken;

    protected function setUp(): void
    {
        $this->client = self::createClient();

        $container = $this->client->getContainer();
        /** @var EntityManagerInterface $em */
        $em = $container->get(EntityManagerInterface::class);
        $this->tests = new TestRepository($em);

        $fixturesLoader = new FixturesLoader($container);
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
        $this->client->jsonRequest('POST', '/v1/admin/testing/tests');

        self::assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testForbiddenForRegularUser(): void
    {
        $this->client->jsonRequest(
            'POST',
            '/v1/admin/testing/tests',
            [],
            $this->authHeaders($this->userToken)
        );

        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testSuccess(): void
    {
        $this->client->jsonRequest(
            'POST',
            '/v1/admin/testing/tests',
            [
                'name' => RequestFixture::TEST_NAME,
                'cipher' => RequestFixture::TEST_CIPHER,
                'description' => 'Description',
                'numberOfTickets' => 1,
                'numberQuestionsInTicket' => 1,
                'allowedMistakes' => 2,
                'courseIds' => ['17f7f504-d951-4a11-a89b-e78a372c64a3'],
            ],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(201, $this->client->getResponse()->getStatusCode());
    }

    public function testAlready(): void
    {
        $this->client->jsonRequest(
            'POST',
            '/v1/admin/testing/tests',
            [
                'name' => RequestFixture::TEST_NAME,
                'cipher' => RequestFixture::TEST_CIPHER,
                'description' => 'Description',
                'numberOfTickets' => 1,
                'numberQuestionsInTicket' => 1,
                'allowedMistakes' => 2,
                'courseIds' => ['17f7f504-d951-4a11-a89b-e78a372c64a3'],
            ],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(201, $this->client->getResponse()->getStatusCode());

        $this->client->jsonRequest(
            'POST',
            '/v1/admin/testing/tests',
            [
                'name' => RequestFixture::TEST_NAME,
                'cipher' => RequestFixture::TEST_CIPHER,
                'description' => 'Description',
                'numberOfTickets' => 1,
                'numberQuestionsInTicket' => 1,
                'allowedMistakes' => 2,
                'courseIds' => ['17f7f504-d951-4a11-a89b-e78a372c64a3'],
            ],
            $this->authHeaders($this->adminToken)
        );
        self::assertEquals(409, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['message' => 'Test with slug already exists.'], $data);
    }

    public function testEmpty(): void
    {
        $this->client->jsonRequest(
            'POST',
            '/v1/admin/testing/tests',
            [],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(422, $this->client->getResponse()->getStatusCode());
        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'name' => 'This value should not be blank.',
            'cipher' => 'This value should not be blank.',
            'description' => 'This value should not be blank.',
            'numberOfTickets' => 'This value should be greater than 0.',
            'numberQuestionsInTicket' => 'This value should be greater than 0.',
            'allowedMistakes' => 'This value should be greater than 0.',
            'courseIds' => 'This value should not be blank.',
        ]], $data);
    }
}
