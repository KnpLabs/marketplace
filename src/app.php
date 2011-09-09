<?php

$app = require_once __DIR__.'/bootstrap.php';

$app->get('/', function() use ($app) {
    $projects = $app['db']->fetchAll('SELECT * FROM project ORDER BY id DESC LIMIT 5');

    return $app['twig']->render('homepage.html.twig', array(
        'projects' => $projects,
    ));
})->bind('homepage');

$app->get('/project/new', function() use ($app) {
    $form = $app['form.factory']->create(new Form\ProjectType());

    return $app['twig']->render('Project/new.html.twig', array(
        'form' => $form->createView(),
    ));
})->bind('project_new');

$app->post('/project', function() use ($app) {
    $project = $app['request']->get('project');
    $form    = $app['form.factory']->create(new Form\ProjectType());

    $form->bindRequest($app['request']);

    if ($form->isValid()) {
        $project = $form->getData();

        unset($project['id']);

        $app['db']->insert('project', $project);

        return $app->redirect('/');
    }

    return $app['twig']->render('Project/new.html.twig', array(
        'form' => $form->createView(),
    ));

})->bind('project_create');

return $app;