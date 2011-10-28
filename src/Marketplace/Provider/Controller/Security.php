<?php

namespace Marketplace\Provider\Controller;

use Silex\ControllerProviderInterface;
use Silex\Application;
use Silex\ControllerCollection;

class Security implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = new ControllerCollection();

        /**
         * Logout
         */
        $controllers->get('logout', function() use ($app) {
            $app['session']->remove('username');

            return $app->redirect($app['url_generator']->generate('homepage'));
        })->bind('logout');

        return $controllers;
    }
}