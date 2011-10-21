<?php

namespace Provider\Service;

use Silex\ServiceProviderInterface;
use Silex\Application;

use Marketplace\Migration as MarketplaceMigration;

class Migration implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app->before(function() use ($app) {
            $schema    = $app['db']->getSchemaManager()->createSchema();
            $migration = new MarketplaceMigration($app, $schema);

            if (!$migration->hasVersionInfo()) {
                $migration->createVersionInfo();
            }

            if (true === $migration->migrate()) {
                $app['twig']->addGlobal('migration_infos', $migration->getMigrationInfos());
            }
        });
    }
}
