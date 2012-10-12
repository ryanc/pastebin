<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application;

$app->register(new Silex\Provider\DoctrineServiceProvider, array(
    'db.options' => array(
        'driver' => 'pdo_sqlite',
        'path' => __DIR__ . '/../db/pastebin.db',
    ),
));

$app->register(new Silex\Provider\FormServiceProvider);

$app->register(new Silex\Provider\TwigServiceProvider, array(
    'twig.path' => __DIR__ . '/../views',
    'twig.options' => array(
        'cache' => __DIR__ . '/../cache/twig',
)));

$app->register(new Silex\Provider\ValidatorServiceProvider());

$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'translator.messages' => array(),
));

$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__ . '/../logs/pastebin.log',
));

$app->register(new Silex\Provider\SessionServiceProvider);

$app['storage'] = $app->share(function () use ($app) {
    return new Paste\Storage\Storage($app['db'], $app['monolog']);
});

$env = getenv('APP_ENV') ?: 'prod';
$app->register(new Igorw\Silex\ConfigServiceProvider(
    __DIR__ . "/../config/$env.json"
));

$app->error(function (\Exception $ex, $code) use ($app) {
    if ($code !== 404) {
        return;
    }

    $view = $app['twig']->render('error.twig', array(
        'error' => $ex->getMessage(),
    ));

    return new Response($view, $code);
});

return $app;
