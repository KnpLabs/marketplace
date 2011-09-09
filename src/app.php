<?php

$app = require_once __DIR__.'/bootstrap.php';

$app->get('/', function() {
    return 'Hello, Idea Marketplace!';
})->bind('homepage');

return $app;