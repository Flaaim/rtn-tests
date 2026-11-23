<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Testing\Test\SyncQuestions;

use App\Testing\Command\Test\SyncQuestions\Command;
use App\Testing\Command\Test\SyncQuestions\Handler;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Functional\FixturesLoader;

/**
 * @internal
 * @coversNothing
 */
final class RequestActionTest extends KernelTestCase
{
    private ContainerInterface $container;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->container = self::getContainer();

        $fixtureLoader = new FixturesLoader($this->container);
        $fixtureLoader->loadFixtures([RequestFixture::class]);
    }

    public function testSyncQuestions(): void
    {
        /** @var Handler $handler */
        $handler = $this->container->get(Handler::class);

        $handler->handle(new Command(RequestFixture::TEST_ID));
    }
}
