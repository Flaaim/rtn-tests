<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Course\Course\Api;

use App\Course\Api\CourseApi;
use App\Course\Query\Course\CourseFetcher;
use App\Course\Query\Course\CourseFetcherInterface;
use App\Course\Query\Course\GetQuestionsIds\QueryHandler;
use Doctrine\DBAL\Connection;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Functional\FixturesLoader;

/**
 * @internal
 * @coversNothing
 */
final class CourseApiTest extends KernelTestCase
{
    private readonly ContainerInterface $container;
    private readonly CourseFetcherInterface $courseFetcher;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->container = self::getContainer();
        /** @var Connection $conn */
        $conn = $this->container->get(Connection::class);
        $this->courseFetcher = new CourseFetcher($conn);

        $fixturesLoader = new FixturesLoader($this->container);
        $fixturesLoader->loadFixtures([RequestFixture::class]);
    }

    public function testSuccess(): void
    {
        $queryHandler = new QueryHandler($this->courseFetcher);
        $courseApi = new CourseApi($queryHandler);

        $ids = $courseApi->getQuestionIds(RequestFixture::COURSE_ID);
        self::assertCount(2, $ids);

        self::assertEquals([
            '90be077454a14f3d965c4b07645e3769',
            '6724ac7652bc47d6913ab8ca11b2ea36',
        ], $ids);
    }
}
