<?php

declare(strict_types=1);

namespace App\CourseAnswers\MessageHandlers\Course;

use App\Course\Event\CourseRendered;
use App\CourseAnswers\Entity\Payment\Amount;
use App\CourseAnswers\Entity\Product\Product;
use App\CourseAnswers\Entity\Product\ProductId;
use DateTimeImmutable;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CourseRenderedHandler
{
    public function __invoke(CourseRendered $event): void
    {
        $product = new Product(
            ProductId::generate(),
            $event->cipher,
            new Amount('350.00', 'RUB'),
            $event->filePath,
            new DateTimeImmutable(),
        );
    }
}
