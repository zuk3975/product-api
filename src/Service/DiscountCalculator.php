<?php

namespace App\Service;

class DiscountCalculator
{
    public function calculateFinalPrice(int $originalPrice, int $discountPercent): int
    {
        $discountPercent = abs($discountPercent);

        if ($discountPercent >= 100) {
            return 0;
        }

        return (int) round($originalPrice - ($originalPrice * $discountPercent / 100));
    }
}
