<?php

declare(strict_types=1);

namespace Tests\Functional\Subscription\Payment\FailPayment;

use App\Infrastructure\Payment\PaymentType;
use App\Subscription\Entity\Payment\PaymentRepository;
use App\Subscription\Entity\Payment\PaymentStatus;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\Functional\FixturesLoader;

/**
 * @internal
 * @coversNothing
 */
final class RequestActionTest extends WebTestCase
{
    private readonly KernelBrowser $client;
    private readonly ContainerInterface $container;
    private readonly PaymentRepository $payments;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
        $this->client->disableReboot();

        $this->container = $this->client->getContainer();

        $fixturesLoader = new FixturesLoader($this->container);
        $fixturesLoader->loadFixtures([RequestFixture::class]);

        $em = $this->container->get(EntityManagerInterface::class);
        $this->payments = new PaymentRepository($em);
    }

    public function testMetadataNotExists(): void
    {
        $this->client->jsonRequest(
            'POST',
            '/v1/payment-events',
            [
                'event' => 'payment.canceled',
                'object' => [
                    'id' => RequestFixture::EXTERNAL_ID,
                    'status' => 'canceled',
                ],
            ],
        );

        self::assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testSuccess(): void
    {
        $this->client->jsonRequest(
            'POST',
            '/v1/payment-events',
            [
                'event' => 'payment.canceled',
                'object' => [
                    'id' => RequestFixture::EXTERNAL_ID,
                    'status' => 'canceled',
                    'metadata' => ['payment_type' => PaymentType::SUBSCRIPTION->value],
                ],

            ],
        );

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());

        $payment = $this->payments->findByExternalId(RequestFixture::EXTERNAL_ID);

        self::assertEquals(PaymentStatus::FAILED, $payment->getPaymentStatus());
    }

    public function testExternalIdNotFound(): void
    {
        $this->client->jsonRequest(
            'POST',
            '/v1/payment-events',
            [
                'event' => 'payment.canceled',
                'object' => [
                    'id' => RequestFixture::EXTERNAL_ID_NOT_FOUND,
                    'status' => 'canceled',
                    'metadata' => ['payment_type' => PaymentType::SUBSCRIPTION->value],
                ],

            ],
        );

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
}
