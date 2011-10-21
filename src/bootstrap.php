<?php

require_once __DIR__.'/../vendor/silex/autoload.php';
require_once __DIR__.'/../vendor/lightopenid/openid.php';

$app = new Silex\Application();

$app['autoloader']->registerNamespaces(array(
    'Symfony'          => __DIR__.'/../vendor/',
    'Doctrine\\Common' => __DIR__.'/../vendor/doctrine-common/lib',
    'Panda'            => array(__DIR__.'/../vendor/SilexDiscountServiceProvider/src'),
));

$app['autoloader']->registerNamespaceFallbacks(array(__DIR__));

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
use Provider\Service\Hydrate as HydrateServiceProvider;

/** Twig Extensions */
use Marketplace\Twig\MarketplaceExtension;

/** Doctrine stuff */
use Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Validator\Mapping\ClassMetadataFactory;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

$app->register(new SymfonyBridgesServiceProvider());
$app->register(new UrlGeneratorServiceProvider());
$app->register(new SessionServiceProvider());
$app->register(new FormServiceProvider());
$app->register(new DiscountServiceProvider());
$app->register(new ValidatorServiceProvider());

$app['validator.mapping.class_metadata_factory'] = $app->share(function () use ($app) {
    return new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
});

AnnotationRegistry::registerLoader(function($className) {
    return class_exists($className);
});

$app->register(new DoctrineServiceProvider(), array(
    'db.dbal.class_path'    => __DIR__.'/../vendor/doctrine-dbal/lib',
    'db.common.class_path'  => __DIR__.'/../vendor/doctrine-common/lib',
));

$app->register(new TranslationServiceProvider(), array(
  'translator.messages' => array()
));

$app->register(new TwigServiceProvider(), array(
    'twig.path' => array(
        __DIR__.'/Resources/views',
        __DIR__.'/../vendor/Symfony/Bridge/Twig/Resources/views/Form',
    ),
    'twig.class_path' => __DIR__.'/../vendor/silex/vendor/twig/lib',
));

if (is_readable(__DIR__.'/config.php')) {
    require_once __DIR__.'/config.php';
}

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

if (is_readable(__DIR__.'/config.php')) {
    require_once __DIR__.'/config.php';
}

return $app;