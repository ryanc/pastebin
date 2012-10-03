<?php

$app->register(new Silex\Provider\DoctrineServiceProvider, array(
/*
    'db.options' => array(
        'driver'   => 'pdo_pgsql',
        'dbname'   => 'pastebin',
        'host'     => '127.0.0.1',
        'user'     => 'pastebin',
        'password' => 'password',
    ),
*/
    'db.options' => array(
        'driver' => 'pdo_sqlite',
        'path' => __DIR__ . '/../db/pastebin.db',
    ),
));

$app->register(new Silex\Provider\FormServiceProvider);

$app->register(new Silex\Provider\TwigServiceProvider, array(
    'twig.path' => __DIR__ . '/../views',
    'twig.options' => array('cache' => __DIR__ . '/../cache/twig'),
));

$app->register(new Silex\Provider\ValidatorServiceProvider());

$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'translator.messages' => array(),
));

$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__ . '/../logs/pastebin.log',
));

$app->register(new Silex\Provider\SessionServiceProvider);

$app['storage'] = $app->share(function () use ($app) {
    return new Paste\Storage\Storage($app['db']);
});

// Enable debug mode.
$app['debug'] = true;
