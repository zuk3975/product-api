<?php

namespace App\Repository;

use App\Entity\Discount;
use App\Entity\Product;

interface DiscountRepositoryInterface
{
    public function findBestForProduct(Product $product): ?Discount;

}
