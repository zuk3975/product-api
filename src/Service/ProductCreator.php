<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Product;
use App\Exception\BulkOperationException;
use App\Repository\CategoryRepositoryInterface;
use App\Repository\ProductRepositoryInterface;

class ProductCreator
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly CategoryRepositoryInterface $categoryRepository,
    ) {
    }

    /**
     * @param array<int, array{sku: string, name: string, price: int, category: string}> $data
     *
     * @return Product[]
     */
    public function createMany(array $data): array
    {
        if (empty($data)) {
            return [];
        }

        $categories = $this->getCategories($data);
        $products = [];
        $errors = [];

        foreach ($data as $productData) {
            $existingProduct = $this->productRepository->findOneBySku($productData['sku']);

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
            $products[] = $this->productRepository->save($product);
        }

        if ($errors) {
            throw new BulkOperationException($errors);
        }

        return $products;
    }

    /**
     * @param array $products
     *
     * @return array<string, Category>
     */
    private function getCategories(array $products): array
    {
        $categoryNames = array_unique(array_column($products, 'category'));

        if (empty($categoryNames)) {
            return [];
        }

        $categories = $this->categoryRepository->findByNames($categoryNames);

        $categoriesByName = [];
        foreach ($categories as $category) {
            $categoriesByName[$category->getName()] = $category;
        }

        return $categoriesByName;
    }
}
