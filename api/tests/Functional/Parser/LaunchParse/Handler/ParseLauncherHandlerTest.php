<?php

declare(strict_types=1);

namespace Tests\Functional\Parser\LaunchParse\Handler;

use App\Parser\Entity\Task\TaskId;
use App\Parser\Entity\Task\TasksRepository;
use App\Parser\Event\ParseLaunched;
use App\Parser\MessageHandler\ParseLaunchedHandler;
use Doctrine\ORM\EntityManagerInterface;
use DomainException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Tests\Functional\FixturesLoader;
use Tests\Functional\Parser\LaunchParse\RequestFixture;

/**
 * @internal
 * @coversNothing
 */
final class ParseLauncherHandlerTest extends KernelTestCase
{
    private readonly ContainerInterface $container;
    private readonly TasksRepository $tasks;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->container = self::getContainer();

        $fixturesLoader = new FixturesLoader($this->container);
        $fixturesLoader->loadFixtures([RequestFixture::class]);

        /** @var EntityManagerInterface $em */
        $em = $this->container->get(EntityManagerInterface::class);
        $this->tasks = new TasksRepository($em);
    }

    public function testSuccess(): void
    {
        $mockResponse = new MockResponse($result = '{"url":"Test"}', [
            'http_code' => 200,
        ]);
        $mockClient = $this->container->get(HttpClientInterface::class);
        $mockClient->setResponseFactory([$mockResponse]);

        $event = new ParseLaunched(
            RequestFixture::TASK_ID,
            RequestFixture::PARSER_ID,
            'branchId',
            'ticketId',
        );

        $handler = $this->container->get(ParseLaunchedHandler::class);
        $handler($event);

        $task = $this->tasks->get(new TaskId(RequestFixture::TASK_ID));
        self::assertNotNull($task);
        self::assertEquals($result, $task->getDraft());
        self::assertNull($task->getFailedReason());
    }

    public function testSuccessWithRetry(): void
    {
        $mockResponse401 = new MockResponse('Auth error', ['http_code' => 401]);
        $responseAuthRefresh = new MockResponse('{"success": true}', [
            'http_code' => 200,
            'response_headers' => [
                'Set-Cookie: WorkplaceToken=e89a12fb-0227-49ff-884d-918fd9ae6f02; path=/; expires=Wed, 19 Jun 2526 23:24:17 GMT',
                'Set-Cookie: .OLIMPAUTH=; path=/Admin; expires=Mon, 11 Oct 1999 17:00:00 GMT',
                'Set-Cookie: .OLIMPAUTH=pDjbPRpZaz6AO913gO583vOCwin7zOTrWDnuSfQxUItgOslmytwr3UqxatGXBtR1mNl+At4Bq7amoCCrct3qxtO0uZtnU6K10hM+vHgKYBeYN55028I5N7MY07uVX3mT; path=/Admin',
                'Set-Cookie: .OLIMPROLES=; path=/Admin; expires=Mon, 11 Oct 1999 17:00:00 GMT',
            ],
        ]);

        $responseParserSuccess = new MockResponse('{"questions": []}', ['http_code' => 200]);

        $mockClient = $this->container->get(HttpClientInterface::class);
        $mockClient->setResponseFactory([$mockResponse401, $responseAuthRefresh, $responseParserSuccess]);

        $event = new ParseLaunched(
            RequestFixture::TASK_ID,
            RequestFixture::PARSER_ID,
            'branchId',
            'ticketId',
        );

        $handler = $this->container->get(ParseLaunchedHandler::class);

        $handler($event);

        $task = $this->tasks->get(new TaskId(RequestFixture::TASK_ID));
        self::assertNotNull($task);

        self::assertSame(3, $mockClient->getRequestsCount());
    }

    public function testRetryFailed(): void
    {
        $mockResponse = new MockResponse('Auth error', ['http_code' => 401]);

        $mockClient = $this->container->get(HttpClientInterface::class);
        $mockClient->setResponseFactory([$mockResponse]);

        $event = new ParseLaunched(
            RequestFixture::TASK_ID,
            RequestFixture::PARSER_ID,
            'branchId',
            'ticketId',
        );

        $handler = $this->container->get(ParseLaunchedHandler::class);

        $handler($event, true);

        $task = $this->tasks->get(new TaskId(RequestFixture::TASK_ID));

        self::assertNotNull($task->getFailedReason());
    }

    public function testParserNotFound(): void
    {
        $event = new ParseLaunched(
            RequestFixture::TASK_ID,
            RequestFixture::PARSER_NOT_FOUND_ID,
            'branchId',
            'ticketId',
        );

        $handler = $this->container->get(ParseLaunchedHandler::class);

        self::expectException(DomainException::class);
        self::expectExceptionMessage('Parser not found.');
        $handler($event);
    }

    public function testTaskNotFound(): void
    {
        $event = new ParseLaunched(
            RequestFixture::TASK_NOT_FOUND_ID,
            RequestFixture::PARSER_ID,
            'branchId',
            'ticketId',
        );

        $handler = $this->container->get(ParseLaunchedHandler::class);

        self::expectException(DomainException::class);
        self::expectExceptionMessage('Task not found.');
        $handler($event);
    }
}
