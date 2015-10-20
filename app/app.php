<?php

$startTime = microtime(true);

// Bootstrap
require_once __DIR__.'/../vendor/autoload.php';

// Config
$config = array(
    'debug' => true,
    'locale' => 'en',
    'baseUrl' => '',
    'theme' => 'yeti',
    'requireHttps' => false,
    'timer.start' => $startTime,
    'monolog.name' => 'pwx',
    'monolog.level' => \Monolog\Logger::DEBUG,
    'monolog.logfile' => __DIR__ . '/../log/app.log',
    'twig.path' => __DIR__ . '/../src/App/views',
    'twig.options' => array(
        'cache' => __DIR__ . '/../cache/twig',
    ),
    'orm.em.options' => array(
        'mappings' => array(
            array(
                'type' => 'annotation',
                'namespace' => 'App\Entity',
                'path' => __DIR__. '/../src/App/Entity',
            ),
        ),
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

// Register DoctrineOrmServiceProvider service
$app->register(new \Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider(), $config);

// Register translation service
$validLocales = array('en', 'es', 'de');
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'locale_fallbacks'  => array('en'),
    'locale' => isset($_COOKIE['locale']) && in_array($_COOKIE['locale'], $validLocales) ?  $_COOKIE['locale'] : $config['locale'],
));

// Register the yaml translations
$app['translator'] = $app->share($app->extend('translator', function(\Silex\Translator $translator, $app) use ($validLocales) {
    $translator->addLoader('yaml', new Symfony\Component\Translation\Loader\YamlFileLoader());

    foreach ($validLocales as $locale) {
        $translator->addResource('yaml', __DIR__ . '/locales/' . $locale . '.yml', $locale);
    }

    return $translator;
}));

// Register default controller
$app['app.default_controller'] = $app->share(
    function () use ($app) {
        return new \App\Controller\DefaultController($app, $app['twig'], $app['credential_service_em'], $app['request']);
    }
);

// Force to use SSL
if ($config['requireHttps']) {
    $app['controllers']->requireHttps();
}

// Register credential service
$app['credential_service'] = $app->share(
    function () use ($app, $config) {
        return new \App\Model\CredentialService($app['db'], $config);
    }
);

// Register credentialEM service
$app['credential_service_em'] = $app->share(
    function () use ($app, $config) {
        return new \App\Model\CredentialServiceEM($app['orm.em'], $app['crypt_aes_service']);
    }
);

// Register cryptAES service
$app['crypt_aes_service'] = $app->share(
    function () use ($app, $config) {
        return new \App\Model\CryptAESService($config);
    }
);

// Register theme service & set user theme
$app['theme_service'] = $app->share(
    function () use ($app){
        return new \App\Model\ThemeService($app);
    }
);
$app['theme_service']->setUserTheme();

// Map routes to controllers
include __DIR__ . '/routing.php';

return $app;
