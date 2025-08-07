<?php

namespace App\Service;

use App\Entity\Discount;
use App\Entity\Product;

class DiscountFinder
{
    /**
     * @param Discount[] $discounts array of available discounts to choose from
     */
    public function findBestForProduct(Product $product, array $discounts): ?Discount
    {
        // sort discounts by percent, so the first one found should be the biggest one
        usort($discounts, function (Discount $a, Discount $b) {
            return $b->getPercent() <=> $a->getPercent();
        });

        return array_find($discounts, fn (Discount $discount) => $this->isApplicable($discount, $product));
    }

    private function isApplicable(Discount $discount, Product $product): bool
    {
        if (Discount::TARGET_TYPE_PRODUCT === $discount->getTargetType() && $discount->getTargetId() === $product->getId()) {
            return true;
        }

        return Discount::TARGET_TYPE_CATEGORY === $discount->getTargetType() && $product->getCategory()->getId() === $discount->getTargetId();
    }
}
