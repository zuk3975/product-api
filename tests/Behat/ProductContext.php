<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\Repository\ProductRepository;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use App\Service\Seed\ProductSeeder;
use PHPUnit\Framework\Assert;

/**
 * This context class contains the definitions of the steps used by the demo
 * feature file. Learn how to get started with Behat and BDD on Behat's website.
 *
 * @see http://behat.org/en/latest/quick_start.html
 */
final class ProductContext implements Context
{
    public function __construct(
        private readonly ProductSeeder $productSeeder,
        private readonly ProductRepository $productRepository,
    ) {
    }

    /**
     * @When I seed the products
     */
    public function executeProductSeeder(): void
    {
        $this->productSeeder->seedProducts();
    }

    /**
     * @Then the following products should be created in the database:
     * @Then the following products exists in the database:
     */
    public function assertProductsExists(TableNode $table): void
    {
        $allProducts = $this->productRepository->findAll();
        $expected = $table->getHash();

        foreach ($expected as $row) {
            $found = false;
            foreach ($allProducts as $product) {
                if ($product->getSku() === $row['sku']) {
                    $found = true;
                    Assert::assertSame((int) $row['price'], $product->getPrice());
                    Assert::assertEquals($row['name'], $product->getName());
                    Assert::assertEquals($row['category'], $product->getCategory()?->getName());
                }
            }

            if (!$found) {
                throw new \RuntimeException('Product not found in database: ' . json_encode($row));
            }
        }
    }
}
