<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application;

require __DIR__ . '/bootstrap.php';
require __DIR__ . '/controllers.php';

$app->error(function(\Exception $ex, $code) use ($app) {
    if ($code !== 404) {
        return;
    }

    $view = $app['twig']->render('error.twig', array(
        'error' => $ex->getMessage(),
    ));

    return new Response($view, $code);
});

return $app;
