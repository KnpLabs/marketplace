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

$app->register(new SymfonyBridgesExtension());
$app->register(new UrlGeneratorExtension());
$app->register(new FormExtension());

$app->register(new DoctrineExtension());

$app->register(new TwigExtension(), array(
    'twig.path' => array(
        __DIR__.'/Resources/view',
        __DIR__.'/../vendor/Symfony/Bridge/Twig/Resources/views/Form',
    ),
    'twig.class_path' => __DIR__.'/../vendor/silex/vendor/twig/lib',
));

if (is_readable(__DIR__.'/config.php')) {
    require_once __DIR__.'/config.php';
}

return $app;