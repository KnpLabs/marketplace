<?php

$app = require_once __DIR__.'/bootstrap.php';

/**
 * Homepage, lists recent projects
 */
$app->get('/', function() use ($app) {
    $projects     = $app['projects']->findHomepage($app['session']->get('username'));
    $lastProjects = $app['projects']->findLatests($app['session']->get('username'));
    $comments     = $app['comments']->findLatests();

    return $app['twig']->render('homepage.html.twig', array(
        'projects'     => $projects,
        'lastProjects' => $lastProjects,
        'comments'     => $comments,
    ));
})->bind('homepage');

$app->mount('/', new \Marketplace\Provider\Controller\Security());
$app->mount('/project', new \Marketplace\Provider\Controller\Project());
$app->mount('/category', new \Marketplace\Provider\Controller\Category());
$app->mount('/comment', new \Marketplace\Provider\Controller\Comment());

return $app;
