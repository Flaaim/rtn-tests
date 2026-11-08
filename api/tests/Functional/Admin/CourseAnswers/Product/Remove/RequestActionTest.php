<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\CourseAnswers\Product\Remove;

use App\CourseAnswers\Entity\Product\ProductRepository;
use App\SharedDomain\Filesystem\FileSystemPathInterface;
use App\SharedDomain\Filesystem\InMemoryFileSystemPath;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tests\Functional\FixturesLoader;
use Tests\Functional\Json;
use Tests\Functional\OAuthTokenTrait;

/**
 * @internal
 * @coversNothing
 */
final class RequestActionTest extends WebTestCase
{
    use OAuthTokenTrait;
    private readonly KernelBrowser $client;
    private readonly FileSystemPathInterface $fileSystemPath;
    private readonly ProductRepository $products;
    private readonly ContainerInterface $container;
    private string $adminToken;
    private string $userToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();

        $this->container = $this->client->getContainer();

        $this->fileSystemPath = InMemoryFileSystemPath::createReal();

        /** @var EntityManagerInterface $em */
        $em = $this->container->get(EntityManagerInterface::class);
        $this->products = new ProductRepository($em);

        $fixturesLoader = new FixturesLoader($this->container);
        $fixturesLoader->loadFixtures([RequestFixture::class]);

        $this->adminToken = $this->getAccessToken(
            $this->client,
            RequestFixture::ADMIN_EMAIL,
            RequestFixture::ADMIN_PASSWORD,
        );

        $this->userToken = $this->getAccessToken(
            $this->client,
            RequestFixture::USER_EMAIL,
            RequestFixture::USER_PASSWORD,
        );
    }

    protected function tearDown(): void
    {
        $this->fileSystemPath->clear();
        parent::tearDown();
    }

    public function testUnauthenticatedReturns401(): void
    {
        $this->client->jsonRequest('DELETE', '/v1/admin/products/' . RequestFixture::PRODUCT_ID);

        self::assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testForbiddenForRegularUser(): void
    {
        $this->client->jsonRequest(
            'DELETE',
            '/v1/admin/products/' . RequestFixture::PRODUCT_ID,
            [],
            $this->authHeaders($this->userToken)
        );

        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testSuccess(): void
    {
        $filePath = $this->fileSystemPath->getValue() . \DIRECTORY_SEPARATOR . RequestFixture::PRODUCT_FILEPATH;
        $this->createFile($filePath);
        self::assertFileExists($filePath);

        $this->client->jsonRequest(
            'DELETE',
            '/v1/admin/products/' . RequestFixture::PRODUCT_ID,
            [],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(204, $this->client->getResponse()->getStatusCode());

        self::assertFileDoesNotExist($filePath);
    }

    public function testInvalid(): void
    {
        $this->client->jsonRequest(
            'DELETE',
            '/v1/admin/products/invalid',
            [],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(422, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'productId' => 'This is not a valid UUID.',
        ]], $data);
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
