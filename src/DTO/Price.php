<?php

namespace App\DTO;

use Symfony\Component\Serializer\Attribute\SerializedName;

class Price
{
    public const string DEFAULT_CURRENCY = 'EUR';

    public function __construct(
        public readonly int $original,
        public readonly int $final,
        #[SerializedName('discount_percentage')]
        public readonly ?string $discountPercentage = null,
        public readonly string $currency = self::DEFAULT_CURRENCY,
    ) {
    }
}
