<?php

namespace App\Service;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Product;
use App\Repository\DiscountRepositoryInterface;
use App\Repository\ProductRepositoryInterface;

class ProductOutputProvider implements ProviderInterface
{

    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly DiscountRepositoryInterface $discountRepository,
        private readonly ProductOutputBuilder $productOutputBuilder,
    ) {

    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        //@todo
        /** @var Product[] $products */
        $products = $this->productRepository->findAll();

        $productOutputs = [];
        foreach ($products as $product) {
//            $discount = $this->discountRepository->findBestForProduct($product);
            $discount = $this->discountRepository->findAll()[0];
            $productOutputs[]  = $this->productOutputBuilder->build($product, $discount);
        }

        return $productOutputs;
    }
}
