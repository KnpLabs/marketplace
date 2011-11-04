<?php
/**
 * This is the test environnement endpoint (used running behat features for example)
 */
$app = require_once __DIR__.'/../app/marketplace.php';

// The application redirects to Google auth page if
// there is not a username in session, so we had a fake one
$app['session']->set('username', 'edgar.thecat@knplabs.com');

// The application use a Closure to check if the username, so 
// we override this service and use a closure which always
// returns true 
$app['auth'] = $app->share(function() use ($app) {
    return function($username) use ($app) {
      return true;
    };
});

$app->run();