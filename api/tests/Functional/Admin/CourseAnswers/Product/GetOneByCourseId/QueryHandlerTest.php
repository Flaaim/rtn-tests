<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\CourseAnswers\Product\GetOneByCourseId;

use App\CourseAnswers\Query\Product\GetByCourseId\Query;
use App\CourseAnswers\Query\Product\GetByCourseId\QueryHandler;
use App\CourseAnswers\Query\Product\ProductFetcher;
use App\CourseAnswers\Query\Product\ProductFetcherInterface;
use DateTimeImmutable;
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

        $product = $handler->handle(
            new Query(RequestFixture::COURSE_ID),
        );

        self::assertEquals(RequestFixture::PRODUCT_AMOUNT, $product->amount);
        self::assertEquals(RequestFixture::PRODUCT_FILEPATH, $product->filePath);
        self::assertEquals('RUB', $product->currency);
        self::assertEquals(new DateTimeImmutable()->format('Y-m-d'), $product->addedAt);
    }

    public function testNotFound(): void
    {
        $handler = new QueryHandler($this->products);

        $product = $handler->handle(
            new Query(RequestFixture::COURSE_NOT_FOUND_ID),
        );

        self::assertNull($product);
    }
}
