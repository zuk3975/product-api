<?php

namespace App\Service;

use App\Entity\Discount;
use App\Entity\Product;

class DiscountFinder
{
    /**
     * @param Product $product
     * @param Discount[] $discounts array of available discounts to choose from
     *
     * @return Discount|null
     */
    public function findBestForProduct(Product $product, array $discounts): ?Discount
    {
        // sort discounts by percent, so the first one found should be the biggest one
        usort($discounts, function(Discount $a, Discount $b) {
            return $b->getPercent() <=> $a->getPercent();
        });

        return array_find($discounts, fn(Discount $discount) => $this->isApplicable($discount, $product));
    }

    private function isApplicable(Discount $discount, Product $product): bool
    {
        if ($discount->getTargetType() === Discount::TARGET_TYPE_PRODUCT && $discount->getTargetId() === $product->getId()) {
            return true;
        }

        return $discount->getTargetType() === Discount::TARGET_TYPE_CATEGORY && $product->getCategory()->getId() === $discount->getTargetId();
    }
}
