<?php
// Bootstrap
require_once __DIR__.'/../vendor/autoload.php';

// Config
$config = array('debug' => true);

if (file_exists(__DIR__ . '/config.php')) {
    include __DIR__ . '/config.php';
}

// Initialize Silex
$app = new Silex\Application($config);

// Register ControllerServiceProvider service
$app->register(new Silex\Provider\ServiceControllerServiceProvider());

// Register default controller
$app['app.default_controller'] = $app->share(
    function () use ($app) {
        return new \App\Controller\DefaultController();
    }
);

// Map routes to controllers
include __DIR__ . '/routing.php';

return $app;
