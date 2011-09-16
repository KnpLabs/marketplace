<?php

$app = require_once __DIR__.'/bootstrap.php';

/**
 * Homepage, lists recent projects
 */
$app->get('/', function() use ($app) {
    $projects = $app['db']->fetchAll('SELECT * FROM project ORDER BY id DESC LIMIT 5');

    return $app['twig']->render('homepage.html.twig', array(
        'projects' => $projects,
    ));
})->bind('homepage');

/**
 * Adds a comment to a project
 */
 $app->post('/project/{id}/comment', function($id) use ($app) {
    $form = $app['form.factory']->create(new Form\CommentType(), new Entity\Comment());
    $form->bindRequest($app['request']);

    if ($form->isValid()) {
        $comment = (array) $form->getData();

        unset($comment['id']);

        $comment['project_id'] = $id;
        $comment['username']   = $app['session']->get('username');

        $app['db']->insert('comment', $comment);

        return $app->redirect($app['url_generator']->generate('project_show', array('id' => $id)));
    }

    $project  = $app['db']->fetchAssoc('SELECT * FROM project WHERE id = ?', array($id));
    $comments = $app['db']->fetchAll('SELECT * FROM comment WHERE project_id = ?', array($id));

    return $app['twig']->render('Project/show.html.twig', array(
        'form'     => $form->createView(),
        'project'  => $project,
        'comments' => $comments,
    ));
 })->bind('project_comment');

/**
 * Deletes a project
 */
$app->post('/project/{id}/delete', function($id) use ($app) {
   $app['db']->delete('project', array('id' => $id));
   return $app->redirect($app['url_generator']->generate('homepage'));
})->bind('project_delete');

/**
 * Project creation form
 */
$app->get('/project/new', function() use ($app) {
    $form = $app['form.factory']->create(new Form\ProjectType(), new Entity\Project());

    return $app['twig']->render('Project/new.html.twig', array(
        'form' => $form->createView(),
    ));
})->bind('project_new');

/**
 * Project show
 */
$app->get('/project/{id}', function($id) use ($app) {
    $project  = $app['db']->fetchAssoc('SELECT * FROM project WHERE id = ?', array($id));
    $comments = $app['db']->fetchAll('SELECT * FROM comment WHERE project_id = ?', array($id));
    $form     = $app['form.factory']->create(new Form\CommentType(), new Entity\Comment());

    return $app['twig']->render('Project/show.html.twig', array(
        'form'     => $form->createView(),
        'project'  => $project,
        'comments' => $comments,
    ));
})->bind('project_show');

/**
 * Project creation
 */
$app->post('/project', function() use ($app) {
    $form = $app['form.factory']->create(new Form\ProjectType(), new Entity\Project());

    $form->bindRequest($app['request']);

    if ($form->isValid()) {

        $project = (array) $form->getData();

        unset($project['id']);

        $project['username'] = $app['session']->get('username');

        $app['db']->insert('project', $project);

        return $app->redirect('/');
    }

    return $app['twig']->render('Project/new.html.twig', array(
        'form' => $form->createView(),
    ));

})->bind('project_create');

return $app;