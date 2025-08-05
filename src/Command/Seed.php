<?php

namespace App\Command;

use App\Seed\DiscountSeeder;
use App\Exception\SeedException;
use App\Seed\ProductSeeder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:seed',
    description: 'Seed the database with all required initial data',
)]
class Seed
{
    public function __construct(
        private readonly ProductSeeder $productSeeder,
        private readonly DiscountSeeder $discountSeeder,
    ) {
    }

    public function __invoke(OutputInterface $output): int
    {
        try {
            $this->productSeeder->seedProducts();
            $this->discountSeeder->seedDiscounts();
        } catch (SeedException $e) {
            foreach ($e->getErrors() as $error) {
                $output->writeln(sprintf('<comment>%s</comment>', $error));
            }

            return Command::FAILURE;
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

            return Command::FAILURE;
        }

        $output->writeln(sprintf('<info>%s</info>', 'Seeded successfully'));

        return Command::SUCCESS;
    }
}
