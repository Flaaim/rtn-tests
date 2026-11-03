<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Course\Course\Add\Handler\CourseScheduledToRender;

use App\Course\Entity\Course\CourseRepository;
use App\Course\Event\CourseRendered;
use App\Course\Event\CourseScheduledToRender;
use App\Course\MessageHandler\CourseScheduledToRenderHandler;
use App\SharedDomain\Filesystem\InMemoryFileSystemPath;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;
use Tests\Functional\FixturesLoader;

/**
 * @internal
 * @coversNothing
 */
final class CourseScheduledToRenderTest extends KernelTestCase
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

    public function testSuccess(): void
    {
        /** @var InMemoryTransport $transport */
        $transport = $this->container->get('messenger.transport.async');
        $transport->reset();

        $handler = $this->container->get(CourseScheduledToRenderHandler::class);
        $event = new CourseScheduledToRender(
            RequestFixture::COURSE_ID,
            '450.00'
        );

        $handler($event);

        $message = $transport->getSent()[0]->getMessage();

        self::assertInstanceOf(CourseRendered::class, $message);
        self::assertEquals(RequestFixture::COURSE_CIPHER, $message->cipher);
        self::assertEquals('450.00', $message->amount);
        self::assertEquals(RequestFixture::COURSE_ID, $message->courseId);
        $expectedFilePath = RequestFixture::COURSE_ID . \DIRECTORY_SEPARATOR . RequestFixture::COURSE_ID . '.pdf';
        self::assertEquals($expectedFilePath, $message->filePath);

        self::assertFileExists($this->fileSystemPath->getValue() . \DIRECTORY_SEPARATOR . $expectedFilePath);
    }

    public function testDefaultPrice(): void
    {
        /** @var InMemoryTransport $transport */
        $transport = $this->container->get('messenger.transport.async');
        $transport->reset();

        $handler = $this->container->get(CourseScheduledToRenderHandler::class);
        $event = new CourseScheduledToRender(
            RequestFixture::COURSE_ID,
        );

        $handler($event);
        $message = $transport->getSent()[0]->getMessage();

        self::assertInstanceOf(CourseRendered::class, $message);
        self::assertEquals('350.00', $message->amount);
    }
}
