<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\CourseAnswers\Product\GetProducts;

use App\CourseAnswers\Query\Product\GetProducts\ProductDTO;
use App\CourseAnswers\Query\Product\GetProducts\QueryHandler;
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

        $products = $handler->handle();

        $product = $products[0];
        self::assertInstanceOf(ProductDTO::class, $product);
        self::assertNotNull($product->productId);
        self::assertNotNull($product->amount);
        self::assertNotNull($product->addedAt);
        self::assertNotNull($product->cipher);
        self::assertNotNull($product->courseId);
        self::assertNotNull($product->currency);
    }
}
