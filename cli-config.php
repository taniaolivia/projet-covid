<?php
// cli-config.php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use DI\ContainerBuilder;

require __DIR__ . '/vendor/autoload.php';

// Instantiate PHP-DI ContainerBuilder
$containerBuilder = new ContainerBuilder();

$settings = require __DIR__.'/config/settings.php';
$settings($containerBuilder);

$dependencies = require __DIR__.'/config/dependencies.php';
$dependencies($containerBuilder);

// Build PHP-DI Container instance
$container = $containerBuilder->build();

return ConsoleRunner::createHelperSet($container->get(EntityManager::class));
