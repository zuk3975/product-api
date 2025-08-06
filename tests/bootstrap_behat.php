<?php

use Symfony\Component\Dotenv\Dotenv;
use Doctrine\ORM\Tools\SchemaTool;

require dirname(__DIR__).'/vendor/autoload.php';

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env.test');
}

$kernel = new App\Kernel('test', true);
$kernel->boot();
$container = $kernel->getContainer();

/** @var \Doctrine\ORM\EntityManagerInterface $em */
$em = $container->get('doctrine')->getManager();

// Drop and recreate the database schema for a clean state
$connection = $em->getConnection();
$platform = $connection->getDatabasePlatform();

$schemaTool = new SchemaTool($em);
$metadata = $em->getMetadataFactory()->getAllMetadata();

$schemaTool->dropDatabase();
$schemaTool->createSchema($metadata);
