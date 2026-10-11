<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Test\Subscriber;

use App\Infrastructure\Http\EventSubscriber\HandlerExceptionSubscriber;
use DomainException;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

/**
 * @internal
 * @coversNothing
 */
final class HandlerExceptionSubscriptionTest extends TestCase
{
    public function testGetSubscribedEvents(): void
    {
        $subscriber = HandlerExceptionSubscriber::getSubscribedEvents();
        self::assertArrayHasKey(KernelEvents::EXCEPTION, $subscriber);
        self::assertEquals(['onKernelException', 20], $subscriber['kernel.exception']);
    }

    public function testProcessHandlerException(): void
    {
        $envelope = new Envelope(new stdClass());

        $exception = new HandlerFailedException($envelope, [new DomainException('Domain exception')]);
        $subscriber = new HandlerExceptionSubscriber();

        $request = Request::create('http://localhost/api/test');
        $kernel = self::createStub(HttpKernelInterface::class);
        $event = new ExceptionEvent(
            $kernel,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );

        $subscriber->onKernelException($event);
        $throwable = $event->getThrowable();

        self::assertNotNull($throwable);
        self::assertInstanceOf(DomainException::class, $throwable);

        self::assertEquals('Domain exception', $throwable->getMessage());
    }

    public function testIgnoredOtherExceptions(): void
    {
        $originalException = new RuntimeException('Just a runtime error');
        $subscriber = new HandlerExceptionSubscriber();

        $request = Request::create('http://localhost/api/test');
        $kernel = self::createStub(HttpKernelInterface::class);

        $event = new ExceptionEvent(
            $kernel,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $originalException
        );

        $subscriber->onKernelException($event);

        self::assertSame($originalException, $event->getThrowable());
    }
}
