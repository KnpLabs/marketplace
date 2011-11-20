<?php

namespace Marketplace\Provider\Controller;

use Silex\ControllerProviderInterface;
use Silex\Application;
use Silex\ControllerCollection;

class Comment implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
      $controllers = new ControllerCollection();

      $controllers->post('preview', function() use($app) {

        return $app['markdown']($app['request']->get('markdown_content'));
      })->bind('comment_preview');

      return $controllers;
    }

}