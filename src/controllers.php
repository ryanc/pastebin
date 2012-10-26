<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Paste\Form;
use Paste\Entity;

$app->get('/', function () use ($app) {
    // Redirect to /p/new.
    $subRequest = Request::create('/new', 'GET');

    return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
});

$app->get('/new', function () use ($app) {

    $form = $app['form.factory']->createBuilder(new Form\Paste);
    $form = $form->getForm();

    $view = $app['twig']->render('new.html', array(
        'form' => $form->createView()
    ));

    return new Response($view, 200, array(
        'Cache-Control' => 's-maxage=0',
    ));
});

$app->post('/new', function (Request $request) use ($app) {

    $form = $app['form.factory']->createBuilder(new Form\Paste);
    $form = $form->getForm();

    $form->bindRequest($request);

    if ($form->isValid()) {
        $paste = $form->getData();
        $paste->setIp($request->getClientIp());

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

    return $app['twig']->render('new.html', array('form' => $form->createView()));
});

$app->get('/history', function () use ($app) {
    $recentPastes = $app['session']->get('recentPastes');

    $view = $app['twig']->render('history.html', array(
        'history' => $recentPastes,
    ));

    return $view;
});

$app->get('/history/clear', function () use ($app) {
    $app['session']->remove('recentPastes');
    $subRequest = Request::create('/history', 'GET');

    return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
});

$app->get('/p/{id}', function ($id) use ($app) {
    $paste = $app['storage']->get($id);

    if ($paste === false) {
        $app->abort(404, 'This paste does not exist yet.');
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

        'paste' => $paste,
        'id' => $id,
    ));
    $view = $app['twig']->render('paste.html', $viewBag);
    
    return new Response($view, 200, array(
        'Cache-Control' => 's-maxage=' . $app['pastebin.cache.maxage'],
    ));
})
->assert('id', '\w+');

$app->get('/p/{id}/raw/{filename}', function ($id, $filename) use ($app) {
    $paste = $app['storage']->get($id);

    return new Response($paste->getContent(), 200, array(
        'Cache-Control' => 's-maxage=' . $app['pastebin.cache.maxage'],
        'Content-Type'  => 'text/plain',
    ));
})
->assert('id', '\w+')
->value('filename', null);

$app->get('/p/{id}/download', function ($id) use ($app) {
    $paste = $app['storage']->get($id);

    if (null == $filename = $paste->getFilename()) {
        $filename = 'paste-' . $id . '.txt';
    }

    return new Response($paste->getContent(), 200, array(
        'Cache-Control' => 's-maxage=' . $app['pastebin.cache.maxage'],
        'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
    ));
})
->assert('id', '\w+');

$app->get('/p/{id}/clone', function ($id) use ($app) {
    $paste = $app['storage']->get($id);

    $form = $app['form.factory']->createBuilder(new Form\Paste, $paste);
    $form = $form->getForm();

    $view = $app['twig']->render('new.html', array(
        'form' => $form->createView(),
    ));

    return $view;
})
->assert('id', '\w+');

$app->get('/latest', function () use ($app) {
    $id = $app['storage']->getLatest();

    return new RedirectResponse('/p/' . $id);
});

$app->post('/api', function (Request $request) use ($app) {
    $paste = new Entity\Paste();
    $paste->setIp($request->getClientIp());
    $paste->setContent($request->request->get('content'));
    $paste->setFilename($request->request->get('filename'));
    $paste->setHighlight($request->request->get('highlight'));

    $errors = $app['validator']->validate($paste);

    if (count($errors) > 0) {
        $json = json_encode(array(
            'success' => false,
            'message' => "Missing required parameters.",
        ));

        return new Response($json, 400, array(
            'Content-Type' => 'application/json',
        ));
    }

    $id = $app['storage']->save($paste);

    $url = $request->getSchemeAndHttpHost() . '/p/' . $id;

    $response = new Response;
    $response->setStatusCode(201);
    $response->headers->set('Location', $url);

    return $response;

});

$app->get('/about', function () use ($app) {
    $view = $app['twig']->render('about.html');

    return new Response($view, 200, array(
        'Cache-Control' => 's-maxage=' . $app['pastebin.cache.maxage'],
    ));
});
