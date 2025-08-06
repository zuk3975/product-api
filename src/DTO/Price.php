<?php

namespace App\DTO;

class Price
{
    public const string DEFAULT_CURRENCY = 'EUR';

    public function __construct(
        public readonly int $original,
        public readonly int $final,
        public readonly ?string $discountPercentage = null,
        public readonly string $currency = self::DEFAULT_CURRENCY,
    ){
    }
}
