<?php
declare(strict_types=1);

use DI\ContainerBuilder;

use Psr\Container\ContainerInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\Setup;
use Doctrine\DBAL\Configuration as DoctrineConfiguration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;


return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions(
        [
            EntityManager::class => function (ContainerInterface $container): EntityManager {
                $settings = $container->get('settings');

                $config = Setup::createAnnotationMetadataConfiguration(
                    $settings['doctrine']['metadata_dirs'],
                    $settings['doctrine']['dev_mode']
                );

                $config->setMetadataDriverImpl(
                    new AnnotationDriver(
                        new AnnotationReader,
                        $settings['doctrine']['metadata_dirs']
                    )
                );

                /*$config->setMetadataCacheImpl(
                    new FilesystemCache(
                        $settings['doctrine']['cache_dir']
                    )
                );*/

                 return \Doctrine\ORM\EntityManager::create(
                     $settings['doctrine']['connection'],
                     $config
                 );
            }
        ],
        [
            Connection::class => function (ContainerInterface $container) {
                $config = new DoctrineConfiguration();
                $connectionParams = $container->get('settings')['doctrine']['connection'];

                return DriverManager::getConnection($connectionParams, $config);
                },

            PDO::class => function (ContainerInterface $container) {
                return $container->get(Connection::class)->getWrappedConnection();
            }
        ],
    );
};
