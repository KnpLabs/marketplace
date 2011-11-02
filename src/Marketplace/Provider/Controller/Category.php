<?php

namespace Marketplace\Provider\Controller;

use Silex\ControllerProviderInterface;
use Silex\Application;
use Silex\ControllerCollection;

class Category implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = new ControllerCollection();

        /**
         * See projects from a specific category
         */
        $controllers->get('{slug}', function($slug) use ($app) {
            $projects     = $app['projects']->findByCategory($slug, $app['session']->get('username'));
            $lastProjects = $app['projects']->findLatestsByCategory($slug, $app['session']->get('username'));

            return $app['twig']->render('Category/show.html.twig', array(
                'projects'     => $projects,
                'lastProjects' => $lastProjects,
                'category'     => $slug,
            ));
        })->bind('category_show');

        return $controllers;
    }
}