<?php

namespace App\Seed;

use App\Entity\Category;
use App\Entity\Product;
use App\Exception\SeedException;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;

class ProductSeeder
{
    public function __construct(
        private readonly string $productsSeedFilePath,
        private readonly FileSystem $fileSystem,
        private readonly CategoryRepository $categoryRepository,
        private readonly ProductRepository $productRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function seedProducts(): void
    {
        $products = json_decode($this->fileSystem->readFile($this->productsSeedFilePath), true)['products'] ?? [];
        if (!$products) {
            throw new SeedException(['No products found in the seed file']);
        }

        $categories = $this->createCategories($products);

        $errors = [];
        foreach ($products as $productData) {
            $existingProduct = $this->productRepository->findOneBy(['sku' => $productData['sku']]);

            if ($existingProduct) {
                $product = $existingProduct;
            } else {
                $product = new Product();
                $product->setSku($productData['sku']);
            }

            $product->setName($productData['name']);
            $product->setPrice($productData['price']);

            $category = $categories[$productData['category']] ?? null;

            if (!$category) {
                $errors[] = sprintf('Skipping product "%s". Category "%s" not found', $productData['name'], $productData['category']);
                continue;
            }

            $product->setCategory($category);
            $this->entityManager->persist($product);
        }

        $this->entityManager->flush();

        if ($errors) {
            throw new SeedException($errors);
        }
    }

    /**
     * @param array<string, mixed> $products
     *
     * @return array<string, Category> Category entities indexed by category name
     */
    private function createCategories(array $products): array
    {
        $categoryNames = array_unique(array_column($products, 'category'));

        if (empty($categoryNames)) {
            return [];
        }

        $existingCategories = $this->categoryRepository->findBy(['name' => $categoryNames]);
        $existingCategoryMap = [];

        foreach ($existingCategories as $existingCategory) {
            $existingCategoryMap[$existingCategory->getName()] = $existingCategory;
        }

        $categories = [];

        foreach ($categoryNames as $categoryName) {
            if (isset($existingCategoryMap[$categoryName])) {
                $category = $existingCategoryMap[$categoryName];
            } else {
                $category = new Category();
                $category->setName($categoryName);
                $this->entityManager->persist($category);
            }

            $categories[$category->getName()] = $category;
        }

        $this->entityManager->flush();

        return $categories;
    }
}
