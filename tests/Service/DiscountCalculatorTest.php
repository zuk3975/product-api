<?php

namespace App\Tests\Service;

use App\Service\DiscountCalculator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DiscountCalculatorTest extends TestCase
{
    #[dataProvider('getData')]
    public function testItCalculatesFinalPriceCorrectly(int $originalPrice, int $discountPercent, int $expectedFinalPrice): void
    {
        $discountCalculator = new DiscountCalculator();
        $actualFinalPrice = $discountCalculator->calculateFinalPrice($originalPrice, $discountPercent);

        $this->assertSame($expectedFinalPrice, $actualFinalPrice);
    }

    public static function getData(): iterable
    {
        yield [100, 10, 90];
        yield [89000, 15, 75650];
        yield [71000, 15, 60350];
        yield [89000, 30, 62300];
        yield [89000, 30, 62300];
        yield [89000, 33, 59630];
        yield [89000, -33, 59630];
        yield [89000, 100, 0];
        yield [89000, 101, 0];
    }
}
