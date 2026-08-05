<?php

declare(strict_types=1);

namespace Tests\Functional\Testing\Add;

use App\Testing\Event\CourseCreated;
use App\Testing\MessageHandler\CourseCreatedHandler;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class CourseCreatedHandlerTest extends KernelTestCase
{
    public function testSuccess(): void
    {
        self::bootKernel();
        $container = $this->getContainer();

        $handler = $container->get(CourseCreatedHandler::class);
        $message = new CourseCreated('63879491-6883-4e88-8be2-295d3d260346');

        $handler($message);


    }
}
