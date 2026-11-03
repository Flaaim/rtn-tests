<?php

declare(strict_types=1);

namespace App\CourseAnswers\Command\Product\Remove;

use App\Course\Service\Downloader\DirectoryCleanerInterface;
use App\CourseAnswers\Entity\Product\ProductId;
use App\CourseAnswers\Entity\Product\ProductRepository;
use App\Infrastructure\Doctrine\Flusher;
use App\SharedDomain\Filesystem\FileSystemPathInterface;

final readonly class Handler
{
    public function __construct(
        private ProductRepository $products,
        private DirectoryCleanerInterface $cleaner,
        private FileSystemPathInterface $coursePath,
        private Flusher $flusher
    ) {}

    public function handle(Command $command): void
    {
        $product = $this->products->get(new ProductId($command->productId));

        $productDir = $this->coursePath->getValue() . \DIRECTORY_SEPARATOR . $product->getId()->getValue();

        $this->cleaner->cleanDirectory($productDir);

        $this->products->remove($product);

        $this->flusher->flush();
    }
}
