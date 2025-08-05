<?php

namespace App\Seed;

use App\Entity\Discount;
use App\Exception\SeedException;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;

class DiscountSeeder
{
    public function __construct(
        private readonly string $discountsSeedFilePath,
        private readonly FileSystem $fileSystem,
        private readonly CategoryRepository $categoryRepository,
        private readonly ProductRepository $productRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function seedDiscounts(): void
    {
        $discounts = json_decode($this->fileSystem->readFile($this->discountsSeedFilePath), true)['discounts'] ?? [];

        if (!$discounts) {
            throw new SeedException(['No discounts found in the seed file']);
        }

        foreach ($discounts as $discountData) {
            $this->validateData($discountData);

            if (!empty($discountData['sku'])) {
                $targetProduct = $this->productRepository->findOneBy(['sku' => $discountData['sku']]);

                if (!$targetProduct) {
                    return;
                }

                $targetType = Discount::TARGET_TYPE_PRODUCT;
                $targetId = $targetProduct->getId();
            } else {
                $targetCategory = $this->categoryRepository->findOneBy(['name' => $discountData['category']]);

                if (!$targetCategory) {
                    return;
                }

                $targetType = Discount::TARGET_TYPE_CATEGORY;
                $targetId = $targetCategory->getId();
            }

            $discount = new Discount();
            $discount->setTargetType($targetType);
            $discount->setTargetId($targetId);
            $discount->setPercent($discountData['percent']);

            $this->entityManager->persist($discount);
        }

        $this->entityManager->flush();
    }

    private function validateData(array $discountData): void
    {
        if (!$discountData['percent']) {
            throw new SeedException(['Discount percent is required']);
        }

        if (empty($discountData['sku']) && empty($discountData['category'])) {
            throw new SeedException(['Either sku or category is required for discount']);
        }
    }
}
