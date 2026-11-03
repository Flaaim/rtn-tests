<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\CourseAnswers\Product\ChangeAmount;

use App\CourseAnswers\Entity\Product\ProductId;
use App\CourseAnswers\Entity\Product\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    private readonly ContainerInterface $container;
    private readonly ProductRepository $products;
    private string $adminToken;
    private string $userToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();

        $this->container = $this->client->getContainer();

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

    public function testUnauthenticatedReturns401(): void
    {
        $this->client->jsonRequest('PUT', '/v1/admin/products/' . RequestFixture::PRODUCT_ID . '/change-amount');

        self::assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testForbiddenForRegularUser(): void
    {
        $this->client->jsonRequest(
            'PUT',
            '/v1/admin/products/' . RequestFixture::PRODUCT_ID . '/change-amount',
            [],
            $this->authHeaders($this->userToken)
        );

        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testSuccess(): void
    {
        $this->client->jsonRequest(
            'PUT',
            '/v1/admin/products/' . RequestFixture::PRODUCT_ID . '/change-amount',
            [
                'amount' => '120.00',
            ],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(204, $this->client->getResponse()->getStatusCode());

        $product = $this->products->get(new ProductId(RequestFixture::PRODUCT_ID));
        self::assertEquals('120.00', $product->getAmount()->getValue());
    }

    public function testNotFound(): void
    {
        $this->client->jsonRequest(
            'PUT',
            '/v1/admin/products/' . RequestFixture::PRODUCT_NOT_FOUND_ID . '/change-amount',
            [
                'amount' => '120.00',
            ],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(409, $this->client->getResponse()->getStatusCode());
        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['message' => 'Product not found.'], $data);
    }

    public function testInvalid(): void
    {
        $this->client->jsonRequest(
            'PUT',
            '/v1/admin/products/invalid/change-amount',
            [
                'amount' => '',
            ],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(422, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'amount' => 'This value should not be blank.',
            'productId' => 'This is not a valid UUID.',
        ]], $data);
    }
}
