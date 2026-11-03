<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\CourseAnswers\CourseRenderedHandler;

use App\Course\Event\CourseRendered;
use App\CourseAnswers\Entity\Product\ProductRepository;
use App\CourseAnswers\MessageHandlers\Course\CourseRenderedHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @internal
 * @coversNothing
 */
final class CourseRenderedHandlerTest extends KernelTestCase
{
    private readonly ContainerInterface $container;
    private readonly ProductRepository $products;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->container = self::getContainer();

        /** @var EntityManagerInterface $em */
        $em = $this->container->get(EntityManagerInterface::class);
        $this->products = new ProductRepository($em);
    }

    public function testAddNew(): void
    {
        $handler = $this->container->get(CourseRenderedHandler::class);
        $event = new CourseRendered(
            RequestFixture::NEW_CIPHER,
            RequestFixture::NEW_FILE_PATH,
            RequestFixture::NEW_COURSE_ID,
            '450.00'
        );
        $handler($event);

        $product = $this->products->findByCourseId(RequestFixture::NEW_COURSE_ID);

        self::assertEquals(RequestFixture::NEW_CIPHER, $product->getCipher());
        self::assertEquals(RequestFixture::NEW_FILE_PATH, $product->getFilePath());
        self::assertEquals('450.00', $product->getAmount()->getValue());
    }

    public function testUpdateExisting(): void
    {
        $handler = $this->container->get(CourseRenderedHandler::class);
        $event = new CourseRendered(
            'ОТ 555.1',
            'newFilePath',
            RequestFixture::EXISTS_COURSE_ID,
            '250.00',
        );
        $handler($event);

        $product = $this->products->findByCourseId(RequestFixture::EXISTS_COURSE_ID);
        self::assertEquals('ОТ 555.1', $product->getCipher());
        self::assertEquals('newFilePath', $product->getFilePath());
        self::assertEquals('250.00', $product->getAmount()->getValue());
    }
}
