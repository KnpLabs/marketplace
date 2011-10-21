<?php

require_once __DIR__.'/../vendor/silex/autoload.php';
require_once __DIR__.'/../vendor/lightopenid/openid.php';

$app = new Silex\Application();

$app['autoloader']->registerNamespaces(array(
    'Symfony'          => __DIR__.'/../vendor/',
    'Doctrine\\Common' => __DIR__.'/../vendor/doctrine-common/lib',
    'Panda'            => array(__DIR__.'/../vendor/SilexDiscountExtension/src'),
));

$app['autoloader']->registerNamespaceFallbacks(array(__DIR__));

/** Silex Extensions */
use Silex\Extension\SymfonyBridgesExtension;
use Silex\Extension\UrlGeneratorExtension;
use Silex\Extension\TwigExtension;
use Silex\Extension\FormExtension;
use Silex\Extension\DoctrineExtension;
use Silex\Extension\TranslationExtension;
use Silex\Extension\ValidatorExtension;
use Silex\Extension\SessionExtension;
use Panda\DiscountExtension;

/** Twig Extensions */
use Marketplace\Twig\MarketplaceExtension;

/** Doctrine stuff */
use Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Validator\Mapping\ClassMetadataFactory;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

/** Symfony stuff */
use Symfony\Component\HttpFoundation\Response;

$app->register(new SymfonyBridgesExtension());
$app->register(new UrlGeneratorExtension());
$app->register(new SessionExtension());
$app->register(new FormExtension());
$app->register(new DiscountExtension());

$app->register(new ValidatorExtension());

$app['validator.mapping.class_metadata_factory'] = $app->share(function () use ($app) {
    return new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
});

AnnotationRegistry::registerLoader(function($className) {
    return class_exists($className);
});

$app->register(new DoctrineExtension(), array(
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