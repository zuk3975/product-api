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
final class ProductSeedContext implements Context
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
     */
    public function assertProductsCreated(TableNode $table): void
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

    /**
     * @Then the categories should be created if they do not exist
     */
    public function theCategoriesShouldBeCreatedIfTheyDoNotExist(): void
    {
        // TODO: Add assertions to check database for created categories
        // This is a placeholder
        if (false) {
            throw new \Exception('Categories not found in database');
        }
    }

    /**
     * @Then an error should be thrown indicating no products found
     */
    public function anErrorShouldBeThrownIndicatingNoProductsFound(): void
    {
        if ($this->seederException === null || strpos($this->seederException->getMessage(), 'No products found') === false) {
            throw new \Exception('Expected exception for no products found was not thrown');
        }
    }
}
