<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Parser\LaunchParse;

use App\Parser\Entity\Task\TaskId;
use App\Parser\Entity\Task\TasksRepository;
use App\Parser\Event\ParseLaunched;
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
    private KernelBrowser $client;
    private readonly ContainerInterface $container;
    private readonly TasksRepository $tasks;

    private string $adminToken;
    private string $userToken;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        $this->client->disableReboot();

        $this->container = $this->client->getContainer();
        $fixturesLoader = new FixturesLoader($this->container);
        $fixturesLoader->loadFixtures([RequestFixture::class]);

        /** @var EntityManagerInterface $em */
        $em = $this->container->get(EntityManagerInterface::class);
        $this->tasks = new TasksRepository($em);

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

    public function testUnauthenticatedReturn401(): void
    {
        $this->client->jsonRequest('POST', '/v1/admin/parsers/' . RequestFixture::PARSER_ID . '/launch', [
            'branchId' => 'some_string',
            'ticketId' => 'some_string',
        ]);

        self::assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testForbiddenForRegularUser(): void
    {
        $this->client->jsonRequest(
            'POST',
            '/v1/admin/parsers/' . RequestFixture::PARSER_ID . '/launch',
            [
                'branchId' => 'some_string',
                'ticketId' => 'some_string',
            ],
            $this->authHeaders($this->userToken)
        );

        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testSuccess(): void
    {
        $transport = $this->getContainer()->get('messenger.transport.async');
        $transport->reset();

        $this->client->jsonRequest(
            'POST',
            '/v1/admin/parsers/' . RequestFixture::PARSER_ID . '/launch',
            [
                'branchId' => 'some_string',
                'ticketId' => 'some_string',
            ],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(201, $this->client->getResponse()->getStatusCode());

        self::assertCount(1, $transport->getSent());

        $message = $transport->getSent()[0]->getMessage();
        self::assertInstanceOf(ParseLaunched::class, $message);

        $task = $this->tasks->get(new TaskId($message->taskId));
        self::assertEquals($message->taskId, $task->getId()->getValue());
        self::assertEquals(RequestFixture::PARSER_ID, $task->getParserId()->getValue());
    }

    public function testNotFound(): void
    {
        $this->client->jsonRequest(
            'POST',
            '/v1/admin/parsers/' . RequestFixture::PARSER_NOT_FOUND_ID . '/launch',
            [
                'branchId' => 'some_string',
                'ticketId' => 'some_string',
            ],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(409, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['message' => 'Parser not found.'], $data);
    }

    public function testEmpty(): void
    {
        $this->client->jsonRequest(
            'POST',
            '/v1/admin/parsers/' . RequestFixture::PARSER_ID . '/launch',
            [],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(422, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'branchId' => 'This value should not be blank.',
            'ticketId' => 'This value should not be blank.',
        ]], $data);
    }

    public function testInvalid(): void
    {
        $this->client->jsonRequest(
            'POST',
            '/v1/admin/parsers/invalid/launch',
            [
                'branchId' => 'some_string',
                'ticketId' => 'some_string',
            ],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(422, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'parserId' => 'This is not a valid UUID.',
        ]], $data);
    }
}
