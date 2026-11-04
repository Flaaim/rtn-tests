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
use App\SharedDomain\Filesystem\FileSystemPathInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler]
final readonly class RecipientCreatedHandler
{
    public function __construct(
        private ProductRepository $products,
        private ProductSender $sender,
        private RecipientRepository $recipients,
        private FileSystemPathInterface $coursePath,
        private LoggerInterface $logger,
        private Flusher $flusher
    ) {}

    public function __invoke(RecipientCreated $event): void
    {
        $product = $this->products->get(new ProductId($event->productId));
        $recipient = $this->recipients->get(new RecipientId($event->recipientId));

        $filePath = $this->coursePath->getValue() . \DIRECTORY_SEPARATOR . $product->getFilePath();

        if (!file_exists($filePath)) {
            $recipient->failed("File {$filePath} does not exist");
            $this->flusher->flush();
            return;
        }

        try {
            $this->sender->send(
                $event->email,
                $filePath,
                $product->getCipher()
            );
            $recipient->sent();
        } catch (Throwable $throwable) {
            $recipient->failed(mb_substr($throwable->getMessage(), 0, 255));

            $this->logger->error('Failed to send product: ' . $throwable->getMessage(), [
                'exception' => $throwable,
                'recipientId' => $event->recipientId,
            ]);
        }

        $this->flusher->flush();
    }
}
