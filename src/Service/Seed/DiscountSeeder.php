<?php

namespace App\Service\Seed;

use App\Entity\Discount;
use App\Exception\BulkOperationException;
use App\Repository\CategoryRepositoryInterface;
use App\Repository\DiscountRepositoryInterface;
use App\Repository\ProductRepositoryInterface;
use Symfony\Component\Filesystem\Filesystem;

class DiscountSeeder
{
    public function __construct(
        private readonly string $discountsSeedFilePath,
        private readonly Filesystem $fileSystem,
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly DiscountRepositoryInterface $discountRepository,
    ) {
    }

    public function seedDiscounts(): void
    {
        $discounts = json_decode($this->fileSystem->readFile($this->discountsSeedFilePath), true)['discounts'] ?? [];

        if (!$discounts) {
            throw new BulkOperationException(['No discounts found in the seed file']);
        }

        foreach ($discounts as $discountData) {
            $this->validateData($discountData);

            if (!empty($discountData['sku'])) {
                $targetProduct = $this->productRepository->findOneBySku($discountData['sku']);

                if (!$targetProduct) {
                    return;
                }

                $targetType = Discount::TARGET_TYPE_PRODUCT;
                $targetId = $targetProduct->getId();
            } else {
                $targetCategory = $this->categoryRepository->findByNames([$discountData['category']])[0] ?? null;

                if (!$targetCategory) {
                    return;
                }

                $targetType = Discount::TARGET_TYPE_CATEGORY;
                $targetId = $targetCategory->getId();
            }

            $discount = new Discount();
            $discount->setTargetType($targetType);
            $discount->setTargetId($targetId);
            $discount->setPercent(abs($discountData['percent']));

            $this->discountRepository->save($discount);
        }
    }

    private function validateData(array $discountData): void
    {
        if (!$discountData['percent']) {
            throw new BulkOperationException(['Discount percent is required']);
        }

        if (empty($discountData['sku']) && empty($discountData['category'])) {
            throw new BulkOperationException(['Either sku or category is required for discount']);
        }
    }
}
