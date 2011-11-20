<?php

namespace Marketplace\Provider\Service;

use Silex\ServiceProviderInterface;
use Silex\Application;

use Symfony\Component\HttpFoundation\Response;

class Security implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app->before(function() use ($app) {
            $app['session']->start();

            if ($app['request']->get('_route') == 'logout') {
                return;
            }

            if (!$app['session']->has('username')) {
                $openid = new \LightOpenID($_SERVER['SERVER_NAME']);

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

            $app['twig']->addGlobal('username', $app['session']->get('username'));
        });
    }
}