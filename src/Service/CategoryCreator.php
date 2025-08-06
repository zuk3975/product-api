<?php

namespace App\Service;

use App\Entity\Category;
use App\Repository\CategoryRepositoryInterface;

class CategoryCreator
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
    ) {
    }

    /**
     * @param array<int, array{name: string}> $data
     *
     * @return Category[]
     */
    public function createMany(array $data): array
    {
        $categoryNames = array_column($data, 'name');
        $existingCategories = $this->categoryRepository->findByNames($categoryNames);
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
            }

            $categories[] = $this->categoryRepository->save($category);
        }

        return $categories;
    }
}
