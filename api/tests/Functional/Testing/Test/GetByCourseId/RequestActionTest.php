<?php

declare(strict_types=1);

namespace Tests\Functional\Testing\Test\GetByCourseId;

use App\Testing\Query\Test\GetByCourseId\CategoryDTO;
use App\Testing\Query\Test\GetByCourseId\Query;
use App\Testing\Query\Test\GetByCourseId\QueryHandler;
use App\Testing\Query\Test\TestFetcher;
use App\Testing\Query\Test\TestFetcherInterface;
use Doctrine\DBAL\Connection;
use DomainException;
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

        $fixturesLoader = new FixturesLoader($this->container);
        $fixturesLoader->loadFixtures([RequestFixture::class]);

        /** @var Connection $conn */
        $conn = $this->container->get(Connection::class);
        $this->fetcher = new TestFetcher($conn);
    }

    public function testSuccess(): void
    {
        $handler = new QueryHandler($this->fetcher);

        $query = new Query(RequestFixture::COURSE_ID);

        $test = $handler->handle($query);

        self::assertNotNull($test->testId);
        self::assertNotNull($test->name);
        self::assertNotNull($test->description);
        self::assertNotNull($test->category);

        self::assertInstanceOf(CategoryDTO::class, $test->category);

        $category = $test->category;

        self::assertNotNull($category->id);
        self::assertNotNull($category->name);
        self::assertNotNull($category->slug);
        self::assertNotNull($category->parent);
    }

    public function testNotFound(): void
    {
        $handler = new QueryHandler($this->fetcher);
        $query = new Query(RequestFixture::COURSE_NOT_FOUND_ID);

        self::expectException(DomainException::class);
        self::expectExceptionMessage('Test not found.');
        $handler->handle($query);
    }
}
