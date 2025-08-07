<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductRepository extends ServiceEntityRepository implements ProductRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findMany(array $filters, ?int $limit = null): array
    {
        /** @var Product[] $products */
        $queryBuilder = $this->createQueryBuilder('p')
            ->orderBy('p.sku', 'ASC');

        if (!empty($filters['category'])) {
            $queryBuilder
                ->join('p.category', 'c')
                ->andWhere('c.name = :categoryName')
                ->setParameter('categoryName', $filters['category']);
        }

        if (!empty($filters['priceLessThan'])) {
            $queryBuilder->andWhere('p.price <= :price')
                ->setParameter('price', $filters['priceLessThan']);
        }

        if ($limit) {
            $queryBuilder->setMaxResults($limit);
        }

        /** @var Product[] $products */
        $products = $queryBuilder->getQuery()->getResult();

        return $products;
    }

    public function save(Product $product): Product
    {
        $em = $this->getEntityManager();
        $em->persist($product);
        $em->flush();

        return $product;
    }

    public function findOneBySku(string $sku): ?Product
    {
        /** @var Product|null $result */
        $result = $this->findOneBy(['sku' => $sku]);

        return $result;
    }
}
