<?php

require_once __DIR__.'/../vendor/silex/autoload.php';
require_once __DIR__.'/../vendor/lightopenid/openid.php';

$app = new Silex\Application();

$app['autoloader']->registerNamespaces(array(
    'Symfony'          => __DIR__.'/../vendor/',
    'Doctrine\\Common' => __DIR__.'/../vendor/doctrine-common/lib',
    'Panda'            => __DIR__.'/../vendor/SilexDiscountServiceProvider/src',
));

$app['autoloader']->registerNamespaceFallbacks(array(__DIR__.'/../src/'));

/** Silex Extensions */
use Silex\Provider\SymfonyBridgesServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Panda\DiscountServiceProvider;

/** Marketplace service providers */
use Provider\Service\Security as SecurityServiceProvider;
use Provider\Service\Migration as MigrationServiceProvider;
use Provider\Service\Repository as RepositoryServiceProvider;

/** Twig Extensions */
use Marketplace\Twig\MarketplaceExtension;

$app->register(new SymfonyBridgesServiceProvider());
$app->register(new UrlGeneratorServiceProvider());
$app->register(new SessionServiceProvider());
$app->register(new FormServiceProvider());
$app->register(new DiscountServiceProvider());
$app->register(new ValidatorServiceProvider());

$app->register(new DoctrineServiceProvider(), array(
    'db.dbal.class_path'    => __DIR__.'/../vendor/doctrine-dbal/lib',
    'db.common.class_path'  => __DIR__.'/../vendor/doctrine-common/lib',
));

$app->register(new TranslationServiceProvider(), array(
  'translator.messages' => array()
));

$app->register(new TwigServiceProvider(), array(
    'twig.path' => array(
        __DIR__.'/../src/Resources/views',
        __DIR__.'/../vendor/Symfony/Bridge/Twig/Resources/views/Form',
    ),
    'twig.class_path' => __DIR__.'/../vendor/silex/vendor/twig/lib',
));

if (!file_exists(__DIR__.'/config.php')) {
    throw new RuntimeException('You must create your own configuration file ("src/config.php"). See "src/config.php.dist" for an example config file.');
}

require_once __DIR__.'/config.php';

/** Marketplace providers */
$app->register(new SecurityServiceProvider());
$app->register(new MigrationServiceProvider());
$app->register(new RepositoryServiceProvider(), array('repository.repositories' => array(
    'projects'      => 'Repository\\Project',
    'comments'      => 'Repository\\Comment',
    'project_votes' => 'Repository\\ProjectVote',
    'project_links' => 'Repository\\ProjectLink',
)));

$app->before(function() use ($app) {
    $app['twig']->addGlobal('categories', $app['project.categories']);
    $app['twig']->addExtension(new MarketplaceExtension($app));
});

return $app;
