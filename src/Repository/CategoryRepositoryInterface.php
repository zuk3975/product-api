<?php

namespace App\Repository;

use App\Entity\Category;

interface CategoryRepositoryInterface
{
    public function save(Category $category): Category;

    /**
     * @param string[] $names
     *
     * @return Category[]
     */
    public function findByNames(array $names): array;
}
