<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Testing\Test\Rename;

use App\Testing\Entity\Test\TestId;
use App\Testing\Entity\Test\TestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\Functional\Admin\Testing\Test\Remove\RequestFixture;
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
    private readonly ContainerInterface $container;
    private string $adminToken;
    private string $userToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
        $this->client->disableReboot();

        $this->container = $this->client->getContainer();
        /** @var EntityManagerInterface $em */
        $em = $this->container->get(EntityManagerInterface::class);
        $this->tests = new TestRepository($em);

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
        $this->client->jsonRequest('PUT', '/v1/admin/testing/tests/' . RequestFixture::TEST_ID . '/rename');

        self::assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testForbiddenForRegularUsers(): void
    {
        $this->client->jsonRequest(
            'PUT',
            '/v1/admin/testing/tests/' . RequestFixture::TEST_ID . '/rename',
            [],
            $this->authHeaders($this->userToken)
        );

        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testSuccess(): void
    {
        $this->client->jsonRequest(
            'PUT',
            '/v1/admin/testing/tests/' . RequestFixture::TEST_ID . '/rename',
            [
                'name' => 'Использование (применение) средств индивидуальной защиты',
                'description' => 'Описание теста',
            ],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(204, $this->client->getResponse()->getStatusCode());

        $test = $this->tests->get(new TestId(RequestFixture::TEST_ID));

        self::assertEquals('Использование (применение) средств индивидуальной защиты', $test->getName());
        self::assertEquals('Описание теста', $test->getDescription());
    }

    public function testEmpty(): void
    {
        $this->client->jsonRequest(
            'PUT',
            '/v1/admin/testing/tests/' . RequestFixture::TEST_NOT_FOUND_ID . '/rename',
            [
                'name' => 'Использование (применение) средств индивидуальной защиты',
                'description' => 'Описание теста',
            ],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(409, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['message' => 'Test not found.'], $data);
    }

    public function testInvalid(): void
    {
        $this->client->jsonRequest(
            'PUT',
            '/v1/admin/testing/tests/' . RequestFixture::TEST_ID . '/rename',
            [],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(422, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'name' => 'This value should not be blank.',
            'description' => 'This value should not be blank.',
        ]], $data);
    }
}
