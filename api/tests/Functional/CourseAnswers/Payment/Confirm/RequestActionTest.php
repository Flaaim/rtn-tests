<?php

declare(strict_types=1);

namespace Tests\Functional\CourseAnswers\Payment\Confirm;

use App\CourseAnswers\Entity\Payment\PaymentRepository;
use App\CourseAnswers\Entity\Payment\PaymentStatus;
use App\Infrastructure\Payment\PaymentType;
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

    public function testSuccess(): void
    {
        $this->client->catchExceptions(false);
        $this->client->jsonRequest(
            'POST',
            '/v1/payment-events',
            [
                'event' => 'payment.succeeded',
                'object' => [
                    'id' => RequestFixture::EXTERNAL_ID,
                    'status' => 'succeeded',
                ],
                'metadata' => ['payment_type' => PaymentType::PRODUCT->value],
            ],
        );

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());

        $payment = $this->payments->findByExternalId(RequestFixture::EXTERNAL_ID);

        self::assertEquals(PaymentStatus::SUCCEEDED, $payment->getStatus());
    }

    public function testExternalIdNotFound(): void
    {
        $this->client->jsonRequest(
            'POST',
            '/v1/payment-events',
            [
                'event' => 'payment.succeeded',
                'object' => [
                    'id' => RequestFixture::EXTERNAL_ID_NOT_FOUND,
                    'status' => 'succeeded',
                ],
                'metadata' => ['payment_type' => PaymentType::PRODUCT->value],
            ],
        );

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
}
