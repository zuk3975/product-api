<?php

namespace App\Service\Seed;

use App\Exception\AppException;
use App\Service\CategoryCreator;
use App\Service\ProductCreator;
use Symfony\Component\Filesystem\Filesystem;

class ProductSeeder
{
    public function __construct(
        private readonly string $productsSeedFilePath,
        private readonly FileSystem $fileSystem,
        private readonly ProductCreator $productCreator,
        private readonly CategoryCreator $categoryCreator,
    ) {
    }

    public function seedProducts(): void
    {
        $products = json_decode($this->fileSystem->readFile($this->productsSeedFilePath), true)['products'] ?? [];

        if (!$products) {
            throw new AppException('No products found in the seed file');
        }

        $this->seedCategories($products);
        $this->productCreator->createMany($products);
    }

    /**
     * @param array<string, mixed> $products
     */
    private function seedCategories(array $products): void
    {
        $categoryNames = array_unique(array_column($products, 'category'));

        if (empty($categoryNames)) {
            return;
        }

        $this->categoryCreator->createMany(array_map(fn(string $name) => ['name' => $name], $categoryNames));
    }
}
