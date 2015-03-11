<?php
$startTime = microtime(true);

// Bootstrap
require_once __DIR__.'/../vendor/autoload.php';

// Config
$config = array(
    'debug' => true,
    'timer.start' => $startTime,
    'monolog.name' => 'pwx',
    'monolog.level' => \Monolog\Logger::DEBUG,
    'monolog.logfile' => __DIR__ . '/../log/app.log',
    'twig.path' => __DIR__ . '/../src/App/views',
    'twig.options' => array(
        'cache' => __DIR__ . '/../cache/twig',
    ),
);

if (file_exists(__DIR__ . '/config.php')) {
    include __DIR__ . '/config.php';
}

// Initialize Silex
$app = new App\Silex\Application($config);

// Register ControllerServicEProvider service
$app->register(new Silex\Provider\ServiceControllerServiceProvider());

$app->register(new Silex\Provider\DoctrineServiceProvider(), $config);

// Register default controller
$app['app.default_controller'] = $app->share(
    function () use ($app) {
        return new \App\Controller\DefaultController($app, $app['twig'], $app['logger'], $app['credential_service'], $app['request']);
    }
);

// Register credential service
$app['credential_service'] = $app->share(
    function () use ($app) {
        return new \App\Model\CredentialService($app['db']);
    }
);

// Map routes to controllers
include __DIR__ . '/routing.php';

return $app;
