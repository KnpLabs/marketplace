<?php

namespace Provider\Service;

use Silex\ServiceProviderInterface;
use Silex\Application;

class Hydrate implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['hydrate'] = $app->share(function() {
            return function($entity, $data) {
                foreach ($data as $key => $value) {
                    $entity->$key = $value;
                }

                return $entity;
            };
        });
    }
}
