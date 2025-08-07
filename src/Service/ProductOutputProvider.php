<?php

namespace App\Service;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\DiscountRepositoryInterface;
use App\Repository\ProductRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

class ProductOutputProvider implements ProviderInterface
{
    private const int MAX_LIMIT = 5;

    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly DiscountRepositoryInterface $discountRepository,
        private readonly ProductOutputBuilder $productOutputBuilder,
        private readonly DiscountFinder $discountFinder
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $limit = self::MAX_LIMIT;
        $request = $context['request'] ?? null;
        $filters = [];

        if ($request instanceof Request) {
            $limit = $request->query->get('limit');

            if ($limit > self::MAX_LIMIT) {
                $limit = self::MAX_LIMIT;
            }

            $filters = [
                'category' => $request->query->get('category'),
                'priceLessThan' => $request->query->get('priceLessThan'),
            ];
        }

        $products = $this->productRepository->findMany($filters, $limit);
        if (!$products) {
            return [];
        }

        // finding all discounts should be performant unless there are thousands of discounts (which is not expected anytime soon),
        // in that case only discounts related to found products should be fetched using more complex sql query
        $allDiscounts = $this->discountRepository->findAll();

        $productOutputs = [];
        foreach ($products as $product) {
            $discount = $this->discountFinder->findBestForProduct($product, $allDiscounts);
            $productOutputs[]  = $this->productOutputBuilder->build($product, $discount);
        }

        return $productOutputs;
    }
}
