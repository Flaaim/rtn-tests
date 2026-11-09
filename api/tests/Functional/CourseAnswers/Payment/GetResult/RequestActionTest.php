<?php

declare(strict_types=1);

namespace Tests\Functional\CourseAnswers\Payment\GetResult;

use Psr\Container\ContainerInterface;
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
    private readonly ContainerInterface $container;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
        $this->client->disableReboot();

        $this->container = $this->client->getContainer();

        $fixturesLoader = new FixturesLoader($this->container);
        $fixturesLoader->loadFixtures([RequestFixture::class]);
    }

    public function testSuccess(): void
    {
        $this->client->jsonRequest(
            'GET',
            '/v1/products/payments/' . RequestFixture::PAYMENT_ID . '/result',
        );

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertArrayHasKey('paymentId', $data);
        self::assertArrayHasKey('amount', $data);
        self::assertArrayHasKey('currency', $data);
        self::assertArrayHasKey('email', $data);
        self::assertArrayHasKey('status', $data);
        self::assertArrayHasKey('createdAt', $data);
    }

    public function testInvalid(): void
    {
        $this->client->jsonRequest(
            'GET',
            '/v1/products/payments/invalid/result',
        );

        self::assertEquals(422, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'paymentId' => 'This is not a valid UUID.',
        ]], $data);
    }

    public function testNotFound(): void
    {
        $this->client->jsonRequest(
            'GET',
            '/v1/products/payments/' . RequestFixture::PAYMENT_ID_NOT_FOUND . '/result',
        );

        self::assertEquals(409, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['message' => 'Payment result not found.'], $data);
    }
}
