<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Testing\Test\GetAllTestIdByCourseId;

use App\Testing\Query\Test\GetAllTestIdByCourseId\Query;
use App\Testing\Query\Test\GetAllTestIdByCourseId\QueryHandler;
use App\Testing\Query\Test\TestFetcher;
use App\Testing\Query\Test\TestFetcherInterface;
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
    private ContainerInterface $container;
    private TestFetcherInterface $fetcher;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->container = self::getContainer();

        /** @var Connection $conn */
        $conn = $this->container->get(Connection::class);
        $this->fetcher = new TestFetcher($conn);

        $fixturesLoader = new FixturesLoader($this->container);
        $fixturesLoader->loadFixtures([RequestFixture::class]);
    }

    public function testSuccess(): void
    {
        $queryHandler = new QueryHandler($this->fetcher);

        $query = new Query(RequestFixture::COURSE_ID);

        $tests = $queryHandler->handle($query);

        self::assertCount(1, $tests);
        $test = $tests[0];

        self::assertNotNull($test->testId);
    }
}
