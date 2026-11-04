<?php

declare(strict_types=1);

namespace App\CourseAnswers\MessageHandlers;

use App\CourseAnswers\Entity\Product\ProductId;
use App\CourseAnswers\Entity\Product\ProductRepository;
use App\CourseAnswers\Entity\Recipient\RecipientId;
use App\CourseAnswers\Entity\Recipient\RecipientRepository;
use App\CourseAnswers\Event\RecipientCreated;
use App\CourseAnswers\Service\ProductSender;
use App\Infrastructure\Doctrine\Flusher;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class RecipientCreatedHandler
{
    public function __construct(
        private ProductRepository $products,
        private ProductSender $sender,
        private RecipientRepository $recipients,
        private Flusher $flusher
    ) {}

    public function __invoke(RecipientCreated $event): void
    {
        $product = $this->products->get(new ProductId($event->productId));

        $this->sender->send(
            $event->email,
            $product->getFilePath(),
            $product->getCipher()
        );

        $recipient = $this->recipients->get(new RecipientId($event->recipientId));

        $recipient->sent();

        $this->flusher->flush();
    }
}
