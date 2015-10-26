<?php

$startTime = microtime(true);

// Bootstrap
require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Silex\Application;

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
$app->register(new Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider(), $config);

// Register theme service & set user theme
$app['i18n_service'] = $app->share(function () { return new App\Service\I18nService(); });

// Register translation service
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'locale_fallbacks'  => array('en'),
    'locale' => $app['i18n_service']->getLocale($config['locale']),
));

// Register the yaml translations
$app['translator'] = $app->share($app->extend('translator', function(Silex\Translator $translator, $app) {
    $translator->addLoader('yaml', new Symfony\Component\Translation\Loader\YamlFileLoader());

    foreach ($app['i18n_service']->getValidLocales() as $locale) {
        $translator->addResource('yaml', __DIR__ . '/locales/' . $locale . '.yml', $locale);
    }

    return $translator;
}));

// Register Credentials factory
$app['credentials_factory'] = $app->share(
    function () {
        return new App\Factory\CredentialsFactory();
    }
);

// Register Credentials service
$app['credentials_service'] = $app->share(
    function () use ($app, $config) {
        return new App\Service\CredentialsService(
            $app['orm.em'],
            $app['credentials_factory'],
            $app['orm.em']->getRepository('App\Entity\Credentials'),
            $config
        );
    }
);

// Register default controller
$app['app.default_controller'] = $app->share(
    function () use ($app) {
        return new App\Controller\DefaultController(
            $app,
            $app['twig'],
            $app['credentials_service']
        );
    }
);

// Force to use SSL
if ($config['requireHttps']) {
    $app['controllers']->requireHttps();
}

// Register theme service & set user theme
$app['theme_service'] = $app->share(
    function () use ($app){
        return new App\Service\ThemeService($app);
    }
);
$app['theme_service']->setUserTheme();

// After middleware
$app->after(function (Request $request, Response $response) use ($app) {
    // Set the locale cookie
    $app['i18n_service']->setLocaleCookie($request, $response);
});

// Map routes to controllers
include __DIR__ . '/routing.php';

return $app;
