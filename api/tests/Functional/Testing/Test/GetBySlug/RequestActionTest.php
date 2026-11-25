<?php

declare(strict_types=1);

namespace Tests\Functional\Testing\Test\GetBySlug;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\Functional\FixturesLoader;
use Tests\Functional\Json;

/**
 * @internal
 * @coversNothing
 */
final class RequestActionTest extends WebTestCase
{
    private readonly KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
        $this->client->disableReboot();

        $container = $this->client->getContainer();

        $fixturesLoader = new FixturesLoader($container);
        $fixturesLoader->loadFixtures([RequestFixture::class]);
    }

    public function testSuccess(): void
    {
        $this->client->jsonRequest(
            'GET',
            '/v1/testing/tests/' . RequestFixture::TEST_ACTIVE_SLUG
        );

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertCount(5, $data['tickets']);

        self::assertArrayHasKey('id', $data);
        self::assertArrayHasKey('name', $data);
        self::assertArrayHasKey('cipher', $data);
        self::assertArrayHasKey('description', $data);
        self::assertArrayHasKey('slug', $data);
        self::assertArrayHasKey('createdAt', $data);
        self::assertArrayHasKey('number', $data['tickets'][0]);
        self::assertArrayHasKey('allowedMistakes', $data['settings']);
        self::assertArrayHasKey('numberOfTickets', $data['settings']);
        self::assertArrayHasKey('numberQuestionsInTicket', $data['settings']);
        self::assertArrayHasKey('status', $data);
        self::assertArrayHasKey('products', $data);

        self::assertCount(1, $data['products']);
        $product = $data['products'][0];

        self::assertArrayHasKey('productId', $product);
        self::assertArrayHasKey('name', $product);
        self::assertArrayHasKey('amount', $product);
        self::assertArrayHasKey('addedAt', $product);
    }

    public function testNotActive(): void
    {
        $this->client->jsonRequest(
            'GET',
            '/v1/testing/tests/' . RequestFixture::TEST_SLUG
        );

        self::assertEquals(409, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals([
            'message' => 'No test found for slug ' . RequestFixture::TEST_SLUG,
        ], $data);
    }
}
