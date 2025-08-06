<?php

namespace App\Tests\Behat;

use App\Service\Seed\DiscountSeeder;
use App\Service\Seed\ProductSeeder;
use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;

class CommonContext implements Context
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ProductSeeder $productSeeder,
        private readonly DiscountSeeder $discountSeeder
    ) {
    }

    /**
     * @BeforeScenario @clear-database
     */
    public function clearDatabase(): void
    {
        $schemaTool = new SchemaTool($this->em);
        $metadata = $this->em->getMetadataFactory()->getAllMetadata();

        $schemaTool->dropDatabase();
        $schemaTool->createSchema($metadata);
    }

    /**
     * @BeforeScenario @reset-database
     */
    public function resetDatabase(): void
    {
        $this->clearDatabase();
        $this->productSeeder->seedProducts();
        $this->discountSeeder->seedDiscounts();
    }
}
