# Idea Marketplace

## configuration

You need to add a ``config.php`` file in your ``src/`` directory, that will look like that:

    <?php

    $app['db.options'] = array(
        'driver'   => 'pdo_mysql',
        'dbname'   => 'ideamarketplace',
        'user'     => 'root',
        'password' => '',
    );

    $app['debug'] = true;

    $app['markdown.discount.bin'] = '/usr/local/bin/markdown';

    $app['auth']  = $app->share(function() use ($app) {
        return function($username) use ($app) {
            return (bool) preg_match('/@knplabs\.com$/', $username);
        };
    });

If you don't want to install discount, you can override the service with a dummy one:

    $app['markdown'] = $app->share(function() {
        return function($string) { return $string; };
    });

The ``auth`` service is useful only in production, where people not from KnpLabs should not gain access ;)