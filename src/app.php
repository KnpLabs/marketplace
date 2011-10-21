<?php

use Provider\Controller\Security as SecurityController;
use Provider\Controller\Project as ProjectController;
use Provider\Controller\Category as CategoryController;

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

$app->mount('/', new SecurityController());
$app->mount('/project', new ProjectController());
$app->mount('/category', new CategoryController());

return $app;
