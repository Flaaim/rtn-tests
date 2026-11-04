<?php

declare(strict_types=1);

namespace Tests\Functional\CourseAnswers\RecipientCreatedHandler;

use App\CourseAnswers\Entity\Recipient\RecipientId;
use App\CourseAnswers\Entity\Recipient\RecipientRepository;
use App\CourseAnswers\Event\RecipientCreated;
use App\CourseAnswers\MessageHandlers\RecipientCreatedHandler;
use App\SharedDomain\Filesystem\InMemoryFileSystemPath;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tests\Functional\FixturesLoader;

/**
 * @internal
 * @coversNothing
 */
final class RecipientCreatedHandlerTest extends KernelTestCase
{
    private readonly ContainerInterface $container;
    private readonly RecipientRepository $recipients;
    private readonly InMemoryFileSystemPath $fileSystemPath;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->container = self::getContainer();

        /** @var EntityManagerInterface $em */
        $em = $this->container->get(EntityManagerInterface::class);
        $this->recipients = new RecipientRepository($em);

        $this->fileSystemPath = InMemoryFileSystemPath::createReal();

        $fixtureLoader = new FixturesLoader($this->container);
        $fixtureLoader->loadFixtures([RequestFixture::class]);
    }

    protected function tearDown(): void
    {
        $this->fileSystemPath->clear();
        parent::tearDown();
    }

    public function testSuccess(): void
    {
        $filePath = $this->fileSystemPath->getValue() . \DIRECTORY_SEPARATOR . RequestFixture::PRODUCT_FILEPATH;
        $this->createFile($filePath);

        self::assertFileExists($filePath);
        $recipient = $this->container->get(RecipientCreatedHandler::class);

        $event = new RecipientCreated(
            RequestFixture::RECIPIENT_ID,
            RequestFixture::PRODUCT_ID,
            RequestFixture::RECIPIENT_EMAIL
        );

        $recipient($event);
        $recipient = $this->recipients->get(new RecipientId(RequestFixture::RECIPIENT_ID));

        self::assertEmailCount(1);
        self::assertEmailAddressContains(
            self::getMailerMessage(0),
            'To',
            RequestFixture::RECIPIENT_EMAIL
        );
        self::assertTrue($recipient->isSent());
    }

    public function testFileNotExist(): void
    {
        $recipient = $this->container->get(RecipientCreatedHandler::class);

        $event = new RecipientCreated(
            RequestFixture::RECIPIENT_ID,
            RequestFixture::PRODUCT_ID,
            RequestFixture::RECIPIENT_EMAIL
        );

        $recipient($event);
        $recipient = $this->recipients->get(new RecipientId(RequestFixture::RECIPIENT_ID));

        self::assertEmailCount(0);
        self::assertFalse($recipient->isSent());
        self::assertNotNull($recipient->getFailedReason());
    }

    private function createFile(string $path): void
    {
        $directory = \dirname($path);
        $result = mkdir($directory, 0o777, true);
        if (false === $result) {
            throw new RuntimeException('Unable to create directory ' . $directory);
        }
        $result = file_put_contents($path, '');
        if (false === $result) {
            throw new RuntimeException('Unable to write file ' . $path);
        }
    }
}
