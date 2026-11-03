<?php

declare(strict_types=1);

namespace App\CourseAnswers\Test\Unit\Entity\Product\Command\ChangeAmount;

use App\CourseAnswers\Entity\Payment\Amount;
use App\CourseAnswers\Test\Builder\ProductBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class ChangeAmountTest extends TestCase
{
    public function testChangeAmount(): void
    {
        $product = new ProductBuilder()
            ->withAmount(new Amount('450.00'))
            ->build();

        $product->changeAmount(new Amount('150.00'));

        self::assertEquals('150.00', $product->getAmount()->getValue());
    }
}
