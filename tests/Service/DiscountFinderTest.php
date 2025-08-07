<?php

namespace App\Tests\Service;

use App\Entity\Category;
use App\Entity\Discount;
use App\Entity\Product;
use App\Service\DiscountFinder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DiscountFinderTest extends TestCase
{
    #[dataProvider('getData')]
    public function testFindsHighestDiscountForProduct(array $productData, array $discounts, ?int $expectedDiscountId): void
    {
        $discountMocks = array_map(function(array $discountData) {
            $discount = $this->createMock(Discount::class);
            $discount->method('getId')->willReturn($discountData['discount_id']);
            $discount->method('getTargetId')->willReturn($discountData['target_id']);
            $discount->method('getTargetType')->willReturn($discountData['target_type']);
            $discount->method('getPercent')->willReturn($discountData['percent']);

            return $discount;
        }, $discounts);

        $product = $this->createMock(Product::class);
        $category = $this->createMock(Category::class);
        $category->method('getId')->willReturn($productData['category_id']);

        $product->method('getId')->willReturn($productData['product_id']);
        $product->method('getCategory')->willReturn($category);

        $discountFinder = new DiscountFinder();
        $actual = $discountFinder->findBestForProduct($product, $discountMocks);

        $this->assertEquals($expectedDiscountId, $actual?->getId());
    }

    public static function getData(): iterable
    {
        yield [
            [
                'product_id' => 1,
                'category_id' => 10,
            ],
            [
                [
                    'discount_id' => 1,
                    'target_type' => Discount::TARGET_TYPE_PRODUCT,
                    'target_id' => 1,
                    'percent' => 10
                ],
                [
                    'discount_id' => 2,
                    'target_type' => Discount::TARGET_TYPE_CATEGORY,
                    'target_id' => 10,
                    'percent' => 11
                ],
            ],
            2,
        ];

        yield [
            [
                'product_id' => 1,
                'category_id' => 10,
            ],
            [
                [
                    'discount_id' => 2,
                    'target_type' => Discount::TARGET_TYPE_PRODUCT,
                    'target_id' => 1,
                    'percent' => 15
                ],
                [
                    'discount_id' => 1,
                    'target_type' => Discount::TARGET_TYPE_PRODUCT,
                    'target_id' => 1,
                    'percent' => 10
                ],
            ],
            2,
        ];

        yield [
            [
                'product_id' => 2,
                'category_id' => 12,
            ],
            [
                [
                    'discount_id' => 1,
                    'target_type' => Discount::TARGET_TYPE_CATEGORY,
                    'target_id' => 1,
                    'percent' => 30
                ],
                [
                    'discount_id' => 3,
                    'target_type' => Discount::TARGET_TYPE_CATEGORY,
                    'target_id' => 12,
                    'percent' => 32
                ],
            ],
            3,
        ];

        yield [
            [
                'product_id' => 2,
                'category_id' => 12,
            ],
            [
                [
                    'discount_id' => 1,
                    'target_type' => Discount::TARGET_TYPE_CATEGORY,
                    'target_id' => 5,
                    'percent' => 30
                ],
                [
                    'discount_id' => 3,
                    'target_type' => Discount::TARGET_TYPE_CATEGORY,
                    'target_id' => 7,
                    'percent' => 32
                ],
            ],
            null,
        ];
    }
}
