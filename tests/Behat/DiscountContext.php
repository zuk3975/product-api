<?php

namespace App\Tests\Behat;

use App\Entity\Discount;
use App\Repository\CategoryRepositoryInterface;
use App\Repository\DiscountRepository;
use App\Repository\ProductRepositoryInterface;
use App\Service\Seed\DiscountSeeder;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;

class DiscountContext implements Context
{
    public function __construct(
        private readonly DiscountSeeder $discountSeeder,
        private readonly DiscountRepository $discountRepository,
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly ProductRepositoryInterface $productRepository,
    ) {
    }

    /**
     * @When I seed the discounts
     */
    public function executeDiscountSeeder(): void
    {
        $this->discountSeeder->seedDiscounts();
    }

    /**
     * @Then the following discounts should exist in the database:
     */
    public function assertDiscountsExists(TableNode $table): void
    {
        $allDiscounts = $this->discountRepository->findAll();
        $expected = $table->getHash();
        Assert::assertCount(
            count($expected),
            $allDiscounts,
            'Number of discounts in DB does not match the expected count'
        );


        foreach ($expected as $index =>$row) {
            $discount = $allDiscounts[$index];
            Assert::assertEquals((int) $row['percent'], $discount->getPercent());

            if (!empty($row['category'])) {
                $this->assertCategory($row['category'], $discount);
            }

            if (!empty($row['product'])) {
                $this->assertProduct($row['product'], $discount);
            }
        }
    }

    private function assertCategory(string $categoryName, Discount $discount): void
    {
        Assert::assertEquals(Discount::TARGET_TYPE_CATEGORY, $discount->getTargetType());
        $category = $this->categoryRepository->findByNames([$categoryName])[0] ?? null;

        Assert::assertEquals($discount->getTargetId(), $category->getId());
    }


    private function assertProduct(string $sku, Discount $discount): void
    {
        Assert::assertEquals(Discount::TARGET_TYPE_PRODUCT, $discount->getTargetType());
        $product = $this->productRepository->findOneBySku($sku);
        Assert::assertEquals($discount->getTargetId(), $product?->getId());
    }
}
