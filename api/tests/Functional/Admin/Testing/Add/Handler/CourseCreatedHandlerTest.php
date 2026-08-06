<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Testing\Add\Handler;

use App\Parser\Exception\RemoteException;
use App\SharedDomain\Filesystem\InMemoryFileSystemPath;
use App\Testing\Entity\CourseId;
use App\Testing\Entity\CourseRepository;
use App\Testing\Entity\Status;
use App\Testing\Event\CourseCreated;
use App\Testing\MessageHandler\CourseCreatedHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Tests\Functional\FixturesLoader;

/**
 * @internal
 * @coversNothing
 */
final class CourseCreatedHandlerTest extends KernelTestCase
{
    private readonly ContainerInterface $container;
    private readonly CourseRepository $courses;
    private readonly InMemoryFileSystemPath $fileSystemPath;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->container = self::getContainer();

        /** @var EntityManagerInterface $em */
        $em = $this->container->get(EntityManagerInterface::class);
        $this->courses = new CourseRepository($em);

        $this->fileSystemPath = $this->container->get(InMemoryFileSystemPath::class);

        $fixturesLoader = new FixturesLoader($this->container);
        $fixturesLoader->loadFixtures([RequestFixture::class]);
    }

    protected function tearDown(): void
    {
        $this->fileSystemPath->clear();
    }

    public function testSuccess(): void
    {
        $mockResponse = new MockResponse('', [
            'http_code' => 200,
        ]);
        $mockClient = $this->container->get(HttpClientInterface::class);
        $mockClient->setResponseFactory([$mockResponse]);

        $handler = $this->container->get(CourseCreatedHandler::class);
        $message = new CourseCreated(RequestFixture::COURSE_ID);

        $handler($message);

        $course = $this->courses->get(new CourseId(RequestFixture::COURSE_ID));

        self::assertEquals(Status::created(), $course->getStatus());

        self::assertCount(1, glob('/tmp/phpunit_real_storage/' . RequestFixture::COURSE_ID . '/*.*'));
    }

    public function testFailed(): void
    {
        $mockResponse = new MockResponse('', [
            'http_code' => 404,
        ]);
        $mockClient = $this->container->get(HttpClientInterface::class);
        $mockClient->setResponseFactory([$mockResponse]);

        $handler = $this->container->get(CourseCreatedHandler::class);
        $message = new CourseCreated(RequestFixture::COURSE_ID);

        self::expectException(RemoteException::class);
        $handler($message);

        $course = $this->courses->get(new CourseId(RequestFixture::COURSE_ID));

        self::assertEquals(Status::processing(), $course->getStatus());
        self::assertCount(0, glob('/tmp/phpunit_real_storage/' . RequestFixture::COURSE_ID . '/*.*'));
    }
}
