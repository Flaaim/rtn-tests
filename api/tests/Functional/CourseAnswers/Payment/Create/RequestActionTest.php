<?php

declare(strict_types=1);

namespace Tests\Functional\CourseAnswers\Payment\Create;

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

        $fixtureLoader = new FixturesLoader($container);
        $fixtureLoader->loadFixtures([RequestFixture::class]);
    }

    public function testSuccess(): void
    {
        $this->client->jsonRequest(
            'POST',
            '/v1/products/payments',
            [
                'email' => 'test@email.ru',
                'productId' => RequestFixture::PRODUCT_ID,
                'returnUrl' => 'http://localhost:3000/products/callback',
            ]
        );

        self::assertEquals(201, $this->client->getResponse()->getStatusCode());
        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertArrayHasKey('paymentId', $data);
        self::assertArrayHasKey('confirmationUrl', $data);
        self::assertStringContainsString('/products/callback', $data['confirmationUrl']);
    }
}
