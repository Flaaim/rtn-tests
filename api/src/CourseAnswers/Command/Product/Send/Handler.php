<?php

declare(strict_types=1);

namespace App\CourseAnswers\Command\Product\Send;

use App\CourseAnswers\Entity\Product\ProductId;
use App\CourseAnswers\Entity\Product\ProductRepository;
use App\CourseAnswers\Entity\Recipient\Email;
use App\CourseAnswers\Entity\Recipient\Recipient;
use App\CourseAnswers\Entity\Recipient\RecipientId;
use App\CourseAnswers\Entity\Recipient\RecipientRepository;
use App\Infrastructure\Doctrine\Flusher;
use DateTimeImmutable;
use DomainException;

final readonly class Handler
{
    public function __construct(
        private ProductRepository $products,
        private RecipientRepository $recipients,
        private Flusher $flusher
    ) {}

    public function handle(Command $command): void
    {
        if (!$this->products->hasById(new ProductId($command->productId))) {
            throw new DomainException('Product not found.');
        }

        $recipient = new Recipient(
            RecipientId::generate(),
            $command->productId,
            new Email($command->email),
            new DateTimeImmutable(),
        );

        $this->recipients->add($recipient);

        $this->flusher->flush();
    }
}
