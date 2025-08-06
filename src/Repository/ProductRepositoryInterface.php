<?php

namespace App\Repository;

use App\Entity\Product;

interface ProductRepositoryInterface
{
    public function save(Product $product): Product;

    public function findOneBySku(string $sku): ?Product;
}
