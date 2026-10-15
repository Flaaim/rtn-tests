<?php

declare(strict_types=1);

namespace Tests\Functional\Auth\JoinByMagicLink\Handler;

use App\Auth\Event\MagicLinkRequested;
use App\Auth\MessageHandler\SendMagicLinkOnRequested;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @internal
 * @coversNothing
 */
final class SendMagicLinkOnRequestedHandlerTest extends KernelTestCase
{
    private readonly ContainerInterface $container;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->container = self::getContainer();
    }

    public function testSuccess(): void
    {
        $handler = $this->container->get(SendMagicLinkOnRequested::class);
        $message = new MagicLinkRequested(
            'test@email.ru',
            'token'
        );

        $handler($message);

        self::assertEmailCount(1);
        self::assertEmailAddressContains(self::getMailerMessage(0), 'To', 'test@email.ru');
    }
}
