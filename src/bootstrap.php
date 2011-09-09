<?php

require_once __DIR__.'/../vendor/silex/autoload.php';

$app = new Silex\Application();

$app['autoloader']->registerNamespaces(array(
    'Symfony' => __DIR__.'/../vendor/',
    'Form'    => __DIR__,
));

use Silex\Extension\SymfonyBridgesExtension;
use Silex\Extension\UrlGeneratorExtension;
use Silex\Extension\TwigExtension;
use Silex\Extension\FormExtension;
use Silex\Extension\DoctrineExtension;
use Silex\Extension\TranslationExtension;

$app->register(new SymfonyBridgesExtension());
$app->register(new UrlGeneratorExtension());
$app->register(new FormExtension());

$app->register(new DoctrineExtension(), array(
    'db.dbal.class_path'    => __DIR__.'/../vendor/doctrine-dbal/lib',
    'db.common.class_path'  => __DIR__.'/../vendor/doctrine-common/lib',
));

$app->register(new TranslationExtension(), array(
  'translator.messages' => array()
));

$app->register(new TwigExtension(), array(
    'twig.path' => array(
        __DIR__.'/Resources/views',
        __DIR__.'/../vendor/Symfony/Bridge/Twig/Resources/views/Form',
    ),
    'twig.class_path' => __DIR__.'/../vendor/silex/vendor/twig/lib',
));

if (is_readable(__DIR__.'/config.php')) {
    require_once __DIR__.'/config.php';
}

$app->before(function() use ($app) {
    $manager = $app['db']->getSchemaManager();

    if (count($manager->listTables()) == 0) {
        $schema = new Doctrine\DBAL\Schema\Schema();

        $projectTable = $schema->createTable('project');
        $projectTable->addColumn('id', 'integer', array(
            'unsigned'       => true,
            'autoincrement' => true
        ));
        $projectTable->addColumn('name', 'string');
        $projectTable->addColumn('description', 'text');
        $projectTable->setPrimaryKey(array('id'));
        $projectTable->addUniqueIndex(array('name'));

        $queries = $schema->toSql($app['db']->getDatabasePlatform());

        foreach ($queries as $query) {
            $app['db']->exec($query);
        }
    }
});

return $app;