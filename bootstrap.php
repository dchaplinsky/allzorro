<?php
// bootstrap.php
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;

require_once "vendor/autoload.php";

use Doctrine\ORM\Tools\Setup;
use Dunglas\DoctrineJsonOdm\Serializer;
use Dunglas\DoctrineJsonOdm\Type\JsonDocumentType;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

if (!Type::hasType('json_document')) {
	Type::addType('json_document', JsonDocumentType::class);
	Type::getType('json_document')->setSerializer(
		new Serializer([new ArrayDenormalizer(), new ObjectNormalizer()], [new JsonEncoder()])
	);
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
