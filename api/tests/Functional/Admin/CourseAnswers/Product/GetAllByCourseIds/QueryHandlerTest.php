<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\CourseAnswers\Product\GetAllByCourseIds;

use App\CourseAnswers\Query\Product\GetAllByCourseIds\Query;
use App\CourseAnswers\Query\Product\GetAllByCourseIds\QueryHandler;
use App\CourseAnswers\Query\Product\ProductFetcher;
use App\CourseAnswers\Query\Product\ProductFetcherInterface;
use Doctrine\DBAL\Connection;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Functional\FixturesLoader;

/**
 * @internal
 * @coversNothing
 */
final class QueryHandlerTest extends KernelTestCase
{
    private readonly ContainerInterface $container;

    private readonly ProductFetcherInterface $products;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->container = self::getContainer();
        /** @var Connection $conn */
        $conn = $this->container->get(Connection::class);
        $this->products = new ProductFetcher($conn);

        $fixturesLoader = new FixturesLoader($this->container);
        $fixturesLoader->loadFixtures([RequestFixture::class]);
    }

    public function testSuccess(): void
    {
        $handler = new QueryHandler($this->products);

        $products = $handler->handle(
            new Query(RequestFixture::COURSE_IDS),
        );

        self::assertCount(2, $products);
    }
}
