<?php

declare(strict_types=1);

namespace App\CourseAnswers\Command\Product\ChangeAmount;

use App\CourseAnswers\Entity\Payment\Amount;
use App\CourseAnswers\Entity\Product\ProductId;
use App\CourseAnswers\Entity\Product\ProductRepository;
use App\Infrastructure\Doctrine\Flusher;

final readonly class Handler
{
    public function __construct(
        private ProductRepository $products,
        private Flusher $flusher
    ) {}

    public function handle(Command $command): void
    {
        $product = $this->products->get(new ProductId($command->productId));

        $product->changeAmount(
            new Amount($command->amount)
        );

        $this->flusher->flush();
    }
}
