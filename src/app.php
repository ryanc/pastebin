<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\HttpKernelInterface;

use Paste\Form;

$app = new Silex\Application;

require __DIR__ . '/bootstrap.php';

$app->get('/', function() use ($app) {
    // Redirect to /p/new.
    $subRequest = Request::create('/p/new', 'GET');

    return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
});

$app->get('/p/new', function () use ($app) {

    $form = $app['form.factory']->createBuilder(new Form\Paste);
    $form = $form->getForm();

    $view = $app['twig']->render('new.twig', array(
        'form' => $form->createView()
    ));

    return new Response($view, 200, array(
        'Cache-Control' => 's-maxage=300',
    ));
});

$app->post('/p/new', function(Request $request) use ($app) {

    $form = $app['form.factory']->createBuilder(new Form\Paste);
    $form = $form->getForm();

    $form->bindRequest($request);

    if ($form->isValid()) {
        $paste = $form->getData();

        $id = $app['storage']->save($paste);

        // Save id to the recent pastes.
        $recentPastes = array();

        if (true === $app['session']->has('recentPastes')) {
            $recentPastes = $app['session']->get('recentPastes');
        }

        $recentPastes[] = $id;
        $app['session']->set('recentPastes', $recentPastes);

        return new RedirectResponse('/p/' . $id);
    }

    return $app['twig']->render('new.twig', array('form' => $form->createView()));
});

$app->get('/p/history', function() use ($app) {
    $recentPastes = $app['session']->get('recentPastes');

    $view = $app['twig']->render('history.twig', array(
        'history' => $recentPastes,
    ));

    return $view;
});

$app->get('/p/history/clear', function() use ($app) {
    $app['session']->remove('recentPastes');
    $subRequest = Request::create('/p/history', 'GET');

    return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
});

$app->get('/p/{id}', function($id) use ($app) {
    $paste = $app['storage']->get($id);

    if ($paste === false) {
        $app->abort(404, 'This paste does not exist yet.');
    }

    $view = $app['twig']->render('paste.twig', array(
        'paste' => $paste,
        'id' => $id,
    ));
    
    return new Response($view, 200, array(
        'Cache-Control' => 's-maxage=300',
    ));
})
->assert('id', '\w+');

$app->get('/p/{id}/raw', function($id) use ($app) {
    $paste = $app['storage']->get($id);

    return new Response($paste->getContents(), 200, array(
        'Cache-Control' => 's-maxage=300',
        'Content-Type'  => 'text/plain',
    ));
})
->assert('id', '\w+');

$app->get('/p/{id}/download', function($id) use ($app) {
    $paste = $app['storage']->get($id);

    if (null == $filename = $paste->getFilename()) {
        $filename = 'paste-' . $id . '.txt';
    }

    return new Response($paste->getContents(), 200, array(
        'Cache-Control' => 's-maxage=300',
        'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
    ));
})
->assert('id', '\w+');

$app->get('/p/{id}/dupe', function($id) use ($app) {
    $paste = $app['storage']->get($id);

    $form = $app['form.factory']->createBuilder(new Form\Paste, $paste);
    $form = $form->getForm();

    $view = $app['twig']->render('new.twig', array(
        'form' => $form->createView(),
    ));

    return $view;
})
->assert('id', '\w+');

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
