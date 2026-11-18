<?php

declare(strict_types=1);

namespace Tests\Functional\Profile\GetEmail;

use App\Profile\Query\GetEmail\Query;
use App\Profile\Query\GetEmail\QueryHandler;
use App\Profile\Query\ProfileFetcher;
use App\Profile\Query\ProfileFetcherInterface;
use Doctrine\DBAL\Connection;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Functional\FixturesLoader;

/**
 * @internal
 * @coversNothing
 */
final class RequestActionTest extends KernelTestCase
{
    private readonly ContainerInterface $container;
    private readonly ProfileFetcherInterface $fetcher;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->container = self::getContainer();

        $fixturesLoader = new FixturesLoader($this->container);
        $fixturesLoader->loadFixtures([RequestFixture::class]);

        $conn = $this->container->get(Connection::class);
        $this->fetcher = new ProfileFetcher($conn);
    }

    public function testSuccess(): void
    {
        $handler = new QueryHandler($this->fetcher);
        $query = new Query(RequestFixture::ID);

        $email = $handler->handle($query);

        self::assertEquals(RequestFixture::EMAIL, $email);
    }

    public function testNotFound(): void
    {
        $handler = new QueryHandler($this->fetcher);
        $query = new Query(RequestFixture::NOT_FOUND_ID);

        self::assertNull($handler->handle($query));
    }
}
