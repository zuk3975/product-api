<?php

namespace App\DTO;

class ProductOutput
{
    public function __construct(
        public readonly string $sku,
        public readonly string $name,
        public readonly string $category,
        public readonly Price $price,
    ) {
    }
}
