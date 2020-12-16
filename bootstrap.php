<?php
// bootstrap.php
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;

require_once "vendor/autoload.php";

use Doctrine\ORM\Tools\Setup;

if (!Type::hasType('jsonb')) {
	Type::addType('jsonb', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\Jsonb");
	Type::addType('text[]', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\TextArray");
}

// Create a simple "default" Doctrine ORM configuration for Annotations
$isDevMode = true;
$proxyDir = null;
$cache = null;
$useSimpleAnnotationReader = false;
$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__ . "/src"), $isDevMode, $proxyDir, $cache, $useSimpleAnnotationReader);

// database configuration parameters
$conn = [
	'dbname' => $_ENV['DATABASE_NAME'],
	'user' => $_ENV['DATABASE_USER'],
	'password' => $_ENV['DATABASE_PASSWORD'],
	'host' => $_ENV['DATABASE_HOST'],
	'driver' => 'pdo_pgsql',
];

// obtaining the entity manager
$entityManager = EntityManager::create($conn, $config);

$platform = $entityManager->getConnection()->getDatabasePlatform();
$platform->registerDoctrineTypeMapping('jsonb', 'jsonb');
$platform->registerDoctrineTypeMapping('text[]', 'text[]');
$platform->registerDoctrineTypeMapping('_text', 'text[]');
