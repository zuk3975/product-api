<?php

namespace App\Repository;

use App\Entity\Discount;

interface DiscountRepositoryInterface
{
    public function save(Discount $discount): Discount;

    public function findAll(): array;
}
