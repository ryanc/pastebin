<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application;

$app->register(new Silex\Provider\DoctrineServiceProvider);

$app->register(new Silex\Provider\FormServiceProvider);

$app->register(new Silex\Provider\TwigServiceProvider);

$app->register(new Silex\Provider\ValidatorServiceProvider);

$app->register(new Silex\Provider\TranslationServiceProvider);

$app->register(new Silex\Provider\MonologServiceProvider);

$app->register(new Silex\Provider\SessionServiceProvider);

$app['storage'] = $app->share(function () use ($app) {
    return new Paste\Storage\Storage($app['db'], $app['monolog']);
});

$configReplacements = array(
    'pwd' => __DIR__ . '/../',
);

$env = getenv('APP_ENV') ?: 'prod';
$app->register(new Igorw\Silex\ConfigServiceProvider(
    __DIR__ . "/../config/$env.json.dist", $configReplacements
));
$app->register(new Igorw\Silex\ConfigServiceProvider(
    __DIR__ . "/../config/$env.json", $configReplacements
));

$app['twig']->addGlobal('title', $app['pastebin.title']);
$app['twig']->addGlobal('theme', $app['pastebin.theme']);

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
