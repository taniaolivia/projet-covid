<?php

use DI\ContainerBuilder;

return function(ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        'settings'=> [
            'displayErrorDetails'=> true,
            'doctrine'=> [
                'dev_mode'=>true,
                'cache_dir'=>'/tmp',
                'metadata_dirs'=>[__DIR__.'/../src/Models'],
                'connection' => [
                    'driver'   => 'pdo_mysql',
                    'host' => 'mysql',
                    'user'     => 'tania',
                    'password' => 'tolivia88',
                    'dbname'   => 'PWeb',
                    'charset' => 'utf8',
                    'collation' => 'utf8_unicode_ci',
                    'driverOptions' => [
                        // Turn off persistent connections
                        PDO::ATTR_PERSISTENT => false,
                        // Enable exceptions
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        // Emulate prepared statements
                        PDO::ATTR_EMULATE_PREPARES => true,
                        // Set default fetch mode to array
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        // Set character set
                        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8 COLLATE utf8_unicode_ci'
                    ],
                ],
            ],
        ],

    ]);
};
