<?php

declare(strict_types=1);

namespace App\CourseAnswers\Command\Payment\Create;

use App\CourseAnswers\Entity\Payment\Amount;
use App\CourseAnswers\Entity\Payment\Payment;
use App\CourseAnswers\Entity\Payment\PaymentId;
use App\CourseAnswers\Entity\Payment\PaymentRepository;
use App\CourseAnswers\Entity\Payment\PaymentStatus;
use App\CourseAnswers\Entity\Product\ProductId;
use App\CourseAnswers\Entity\Product\ProductRepository;
use App\Infrastructure\Doctrine\Flusher;
use App\Infrastructure\Payment\PaymentGatewayInterface;
use App\Infrastructure\Payment\PaymentType;

final readonly class Handler
{
    public function __construct(
        private PaymentGatewayInterface $paymentGateway,
        private PaymentRepository $payments,
        private ProductRepository $products,
        private Flusher $flusher
    ) {}

    public function handle(Command $command): array
    {
        $paymentId = PaymentId::generate();
        $productId = $command->productId;

        $product = $this->products->get(new ProductId($productId));

        $email = $command->email;
        $amount = $product->getAmount();
        $status = PaymentStatus::PENDING;

        $returnUrl = $this->appendQueryParameter(
            $command->returnUrl,
            'paymentId',
            $paymentId->getValue(),
        );

        $gatewayResult = $this->paymentGateway->createPayment(
            $email,
            $amount->getValue(),
            $returnUrl,
            ['payment_type' => PaymentType::PRODUCT->value],
        );

        $payment = new Payment(
            $paymentId,
            $email,
            $productId,
            $gatewayResult->externalId,
            $status,
            $amount,
        );

        $this->payments->add($payment);

        $this->flusher->flush();

        return [
            'paymentId' => $payment->getId()->getValue(),
            'confirmationUrl' => $gatewayResult->confirmationUrl,
        ];
    }

    private function appendQueryParameter(string $url, string $key, string $value): string
    {
        $separator = str_contains($url, '?') ? '&' : '?';

        return $url . $separator . rawurlencode($key) . '=' . rawurlencode($value);
    }
}
