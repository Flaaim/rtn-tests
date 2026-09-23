<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Testing\Category\ChangeDescription;

use App\Testing\Entity\Category\CategoryId;
use App\Testing\Entity\Category\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Random\Randomizer;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
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
    private readonly CategoryRepository $categories;
    private string $adminToken;
    private string $userToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
        $this->client->disableReboot();

        $container = $this->client->getContainer();

        $fixturesLoader = new FixturesLoader($container);
        $fixturesLoader->loadFixtures([RequestFixture::class]);

        /** @var EntityManagerInterface $em */
        $em = $container->get(EntityManagerInterface::class);
        $this->categories = new CategoryRepository($em);

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
        $this->client->jsonRequest('PUT', '/v1/admin/testing/categories/' . RequestFixture::CATEGORY_ID . '/change-description');

        self::assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testForbiddenForRegularUsers(): void
    {
        $this->client->jsonRequest(
            'PUT',
            '/v1/admin/testing/categories/' . RequestFixture::CATEGORY_ID . '/change-description',
            [],
            $this->authHeaders($this->userToken),
        );

        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testSuccess(): void
    {
        $this->client->jsonRequest(
            'PUT',
            '/v1/admin/testing/categories/' . RequestFixture::CATEGORY_ID . '/change-description',
            [
                'description' => 'Новое описание',
            ],
            $this->authHeaders($this->adminToken),
        );

        self::assertEquals(204, $this->client->getResponse()->getStatusCode());

        $category = $this->categories->getById(new CategoryId(RequestFixture::CATEGORY_ID));

        self::assertEquals('Новое описание', $category->getDescription());
    }

    public function testEmpty(): void
    {
        $this->client->jsonRequest(
            'PUT',
            '/v1/admin/testing/categories/' . RequestFixture::CATEGORY_ID . '/change-description',
            [],
            $this->authHeaders($this->adminToken),
        );

        self::assertEquals(422, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'description' => 'This value is too short. It should have 1 character or more.',
        ]], $data);
    }

    public function testInvalid(): void
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $randomizer = new Randomizer();
        $description = $randomizer->getBytesFromString($chars, 256);
        $this->client->jsonRequest(
            'PUT',
            '/v1/admin/testing/categories/' . RequestFixture::CATEGORY_ID . '/change-description',
            [
                'description' => $description,
            ],
            $this->authHeaders($this->adminToken),
        );

        self::assertEquals(422, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'description' => 'Описание категории слишком длинное!',
        ]], $data);
    }
}
