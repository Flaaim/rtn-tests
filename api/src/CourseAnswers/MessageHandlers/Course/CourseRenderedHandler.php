<?php

declare(strict_types=1);

namespace App\CourseAnswers\MessageHandlers\Course;

use App\Course\Event\CourseRendered;
use App\CourseAnswers\Entity\Payment\Amount;
use App\CourseAnswers\Entity\Product\Product;
use App\CourseAnswers\Entity\Product\ProductId;
use App\CourseAnswers\Entity\Product\ProductRepository;
use App\Infrastructure\Doctrine\Flusher;
use DateTimeImmutable;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CourseRenderedHandler
{
    public function __construct(
        private ProductRepository $products,
        private Flusher $flusher,
    ) {}

    public function __invoke(CourseRendered $event): void
    {
        $product = new Product(
            ProductId::generate(),
            $event->cipher,
            new Amount($event->amount, 'RUB'),
            $event->filePath,
            new DateTimeImmutable(),
        );

        $this->products->add($product);

        $this->flusher->flush();
    }
}
