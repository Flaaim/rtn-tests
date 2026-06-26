<?php

declare(strict_types=1);

namespace Tests\Functional\Parser\LaunchParse;

use App\Parser\Entity\Task\TaskId;
use App\Parser\Entity\Task\TasksRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tests\Functional\FixturesLoader;
use Tests\Functional\Json;

final class RequestActionTest extends WebTestCase
{
    private KernelBrowser $client;
    private readonly ContainerInterface $container;
    private readonly TasksRepository $tasks;
    public function setUp(): void
    {
        $this->client = static::createClient();

        $this->container = $this->client->getContainer();
        $fixturesLoader = new FixturesLoader($this->container);
        $fixturesLoader->loadFixtures([RequestFixture::class]);

        /** @var EntityManagerInterface $em */
        $em = $this->container->get(EntityManagerInterface::class);
        $this->tasks = new TasksRepository($em);
    }

    public function testSuccess(): void
    {
        $this->client->catchExceptions(false);
        $this->client->jsonRequest('POST', '/v1/parser/launch', [
            'parserId' => RequestFixture::PARSER_ID,
            'branchId' => 'some_string',
            'ticketId' => 'some_string',
        ]);

        self::assertEquals(201, $this->client->getResponse()->getStatusCode());

        $task = $this->tasks->get(new TaskId(RequestFixture::TASK_ID));
        self::assertEquals(RequestFixture::TASK_ID, $task->getId()->getValue());
        self::assertEquals(RequestFixture::PARSER_ID, $task->getParserId()->getValue());
    }

    public function testNotFound(): void
    {
        $this->client->jsonRequest('POST', '/v1/parser/launch', [
            'parserId' => RequestFixture::PARSER_NOT_FOUND_ID,
            'branchId' => 'some_string',
            'ticketId' => 'some_string',
        ]);

        self::assertEquals(409, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['message' => 'Parser not found.'], $data);
    }

    public function testEmpty(): void
    {
        $this->client->jsonRequest('POST', '/v1/parser/launch');

        self::assertEquals(422, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'parserId' => 'This value should not be blank.',
            'branchId' => 'This value should not be blank.',
            'ticketId' => 'This value should not be blank.',
        ]], $data);
    }

    public function testInvalid(): void
    {
        $this->client->jsonRequest('POST', '/v1/parser/launch', [
            'parserId' => 'invalid',
            'branchId' => 'some_string',
            'ticketId' => 'some_string',
        ]);

        self::assertEquals(422, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'parserId' => 'This is not a valid UUID.',
        ]], $data);
    }
}
