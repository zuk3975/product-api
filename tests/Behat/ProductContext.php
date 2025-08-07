<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\Repository\ProductRepository;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use App\Service\Seed\ProductSeeder;
use PHPUnit\Framework\Assert;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

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
        private readonly KernelBrowser $client
    ) {
    }

    /**
     * @When I get products with filters: :filters I should see the following results:
     * @When I get products without filters I should see the following results:
     *
     * @param TableNode $table
     */
    public function getProducts(TableNode $table, string $filters = ''): void
    {
        $this->client->request('GET', '/api/products' . ($filters ? '?' . $filters : ''));

        $response = $this->client->getResponse();
        $expected = $table->getHash();
        $data = json_decode($response->getContent(), true);

        foreach ($expected as $i => $row) {
            $actual = $data[$i];
            Assert::assertEquals($row['sku'], $actual['sku']);
            Assert::assertEquals($row['name'], $actual['name']);
            Assert::assertEquals($row['category'], $actual['category']);
            Assert::assertEquals((int) $row['price_original'], (int) $actual['price']['original']);
            Assert::assertEquals((int) $row['price_final'], (int) $actual['price']['final']);
            Assert::assertEquals($row['discount_percentage'], $actual['price']['discount_percentage']);
            Assert::assertEquals($row['currency'], $actual['price']['currency']);
        }
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
