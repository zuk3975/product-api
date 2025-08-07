<?php

namespace App\Service;

use App\DTO\Price;
use App\DTO\ProductOutput;
use App\Entity\Discount;
use App\Entity\Product;

class ProductOutputBuilder
{
    public function __construct(
        private readonly DiscountCalculator $discountCalculator,
    ) {
    }

    public function build(
        Product $product,
        ?Discount $discount,
    ): ProductOutput {
        $finalPrice = $product->getPrice();
        $percent = null;

        if ($discount) {
            $finalPrice = $this->discountCalculator->calculateFinalPrice($product->getPrice(), $discount->getPercent());
            $percent = $discount->getPercent().'%';
        }

        return new ProductOutput(
            $product->getSku(),
            $product->getName(),
            $product->getCategory()->getName(),
            new Price($product->getPrice(), $finalPrice, $percent)
        );
    }
}
