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

/** Twig Extensions */
use Marketplace\Twig\MarketplaceExtension;

/** Doctrine stuff */
use Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Validator\Mapping\ClassMetadataFactory;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

/** Symfony stuff */
use Symfony\Component\HttpFoundation\Response;

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

$app['hydrate'] = $app->share(function() {
    return function($entity, $data) {
        foreach ($data as $key => $value) {
            $entity->$key = $value;
        }

        return $entity;
    };
});

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

/** Register data repositories */

$dataRepositories = array(
    'projects'      => 'Repository\\Project',
    'comments'      => 'Repository\\Comment',
    'project_votes' => 'Repository\\ProjectVote',
    'project_links' => 'Repository\\ProjectLink',
);

foreach ($dataRepositories as $label => $class) {
    $app[$label] = $app->share(function() use ($class, $app) {
       return new $class($app['db']); 
    });
}

if (is_readable(__DIR__.'/config.php')) {
    require_once __DIR__.'/config.php';
}

$app->before(function() use ($app) {

    $app['session']->start();

    if ($app['request']->get('_route') == 'logout') {
        return;
    }

    if (!$app['session']->has('username')) {
        $openid = new LightOpenID($_SERVER['SERVER_NAME']);

        if (!$openid->mode) {
            $openid->identity = 'https://www.google.com/accounts/o8/id';
            $openid->required = array('email' => 'contact/email');
            return $app->redirect($openid->authUrl());
        } else {
            if ($openid->validate()) {
                $attributes = $openid->getAttributes();
                $app['session']->set('username', $attributes['contact/email']);
            }
        }
    }

    if (isset($app['auth']) && !$app['auth']($app['session']->get('username'))) {
        return new Response($app['twig']->render('forbidden.html.twig'), 403);
    }

    $schema    = $app['db']->getSchemaManager()->createSchema();
    $migration = new Marketplace\Migration($app, $schema);

    if (!$migration->hasVersionInfo()) {
        $migration->createVersionInfo();
    }

    if (true === $migration->migrate()) {
        $app['twig']->addGlobal('migration_infos', $migration->getMigrationInfos());
    }

    $app['twig']->addGlobal('username', $app['session']->get('username'));
    $app['twig']->addGlobal('categories', $app['project.categories']);
    $app['twig']->addExtension(new MarketplaceExtension($app));
});

return $app;