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

// Register ControllerServiceProvider service
$app->register(new Silex\Provider\ServiceControllerServiceProvider());

// Register DoctrineServiceProvider service
$app->register(new Silex\Provider\DoctrineServiceProvider(), $config);

// Register translation service
$translationConfig = array(
    'locale_fallbacks' => array('en'),
);
if (isset($config['locale'])) {
    $translationConfig['locale'] = $config['locale'];
}
$app->register(new Silex\Provider\TranslationServiceProvider(), $translationConfig);

// Register the yaml translations
$app['translator'] = $app->share($app->extend('translator', function($translator, $app) {
    $translator->addLoader('yaml', new Symfony\Component\Translation\Loader\YamlFileLoader());

    $translator->addResource('yaml', __DIR__ . '/locales/en.yml', 'en');
    $translator->addResource('yaml', __DIR__ . '/locales/de.yml', 'de');
    $translator->addResource('yaml', __DIR__ . '/locales/es.yml', 'es');

    return $translator;
}));

// Register default controller
$app['app.default_controller'] = $app->share(
    function () use ($app) {
        return new \App\Controller\DefaultController($app, $app['twig'], $app['credential_service'], $app['request']);
    }
);

// Register credential service
$app['credential_service'] = $app->share(
    function () use ($app, $config) {
        return new \App\Model\CredentialService($app['db'], $config);
    }
);

// Map routes to controllers
include __DIR__ . '/routing.php';

return $app;
