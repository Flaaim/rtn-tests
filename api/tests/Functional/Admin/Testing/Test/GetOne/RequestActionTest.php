<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Testing\Test\GetOne;

use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tests\Functional\Admin\Course\Course\Get\RequestFixture as CourseGetRequestFixture;
use Tests\Functional\ArraySubsetAssertTrait;
use Tests\Functional\FixturesLoader;
use Tests\Functional\Json;
use Tests\Functional\OAuthTokenTrait;

/**
 * @internal
 * @coversNothing
 */
final class RequestActionTest extends WebTestCase
{
    use ArraySubsetAssertTrait;
    use OAuthTokenTrait;

    private readonly KernelBrowser $client;
    private readonly ContainerInterface $container;

    private string $adminToken;
    private string $userToken;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        $this->container = $this->client->getContainer();

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
        $this->client->jsonRequest('GET', '/v1/admin/testing/tests/' . RequestFixture::TEST_ID);

        self::assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testForbiddenForRegularUsers(): void
    {
        $this->client->jsonRequest(
            'GET',
            '/v1/admin/testing/tests/' . RequestFixture::TEST_ID,
            [],
            $this->authHeaders($this->userToken)
        );

        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testSuccess(): void
    {
        $this->client->catchExceptions(false);
        $this->client->jsonRequest(
            'GET',
            '/v1/admin/testing/tests/' . RequestFixture::TEST_ID,
            [],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        $expectedStaticData = [
            'id' => RequestFixture::TEST_ID,
            'name' => RequestFixture::TEST_NAME,
            'cipher' => RequestFixture::TEST_CIPHER,
            'description' => RequestFixture::TEST_NAME,
            'courses' => [
                [
                    'id' => CourseGetRequestFixture::COURSE_ID,
                    'name' => CourseGetRequestFixture::COURSE_NAME,
                ],
            ],
            'slug' => '201',
            'createdAt' => new DateTimeImmutable($data['createdAt'])->format('Y-m-d'),
            'status' => $data['status'],
            'settings' => [
                'allowedMistakes' => $data['settings']['allowedMistakes'],
                'numberOfTickets' => $data['settings']['numberOfTickets'],
                'numberQuestionsInTicket' => $data['settings']['numberQuestionsInTicket'],
            ],
        ];

        self::assertArraySubset($expectedStaticData, $data);
        self::assertArrayHasKey('tickets', $data);
        self::assertCount(5, $data['tickets']);

        $ticket = $data['tickets'][0];
        self::assertEquals(1, $ticket['number']);
        self::assertCount(2, $ticket['questions']);

        $firstQuestion = $ticket['questions'][0];
        self::assertArrayHasKey('id', $firstQuestion);
        self::assertArrayHasKey('text', $firstQuestion);
        self::assertArrayHasKey('answers', $firstQuestion);
        self::assertIsArray($firstQuestion['answers']);
    }

    public function testNotFound(): void
    {
        $this->client->jsonRequest(
            'GET',
            '/v1/admin/testing/tests/' . RequestFixture::TEST_NOT_FOUND_ID,
            [],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(409, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['message' => 'Test not found.'], $data);
    }
}
