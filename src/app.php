<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

$app = new Silex\Application;

require __DIR__ . '/bootstrap.php';

$app->get('/p/new', function () use ($app) {
    $paste = array();

    $form = $app['form.factory']->createBuilder('form', $paste)
        ->add('paste', 'textarea', array(
            'constraints' => new Assert\NotBlank,
        ))
        ->add('filename', 'text', array(
            'required' => false,
        ))
        ->getForm();
    ;

    $view = $app['twig']->render('new.twig', array(
        'form' => $form->createView()
    ));

    return new Response($view, 200, array(
        'Cache-Control' => 's-maxage=300',
    ));
});

$app->post('/p/new', function(Request $request) use ($app) {

    $paste = array();

    $form = $app['form.factory']->createBuilder('form', $paste)
        ->add('paste', 'textarea', array(
            'constraints' => new Assert\NotBlank,
        ))
        ->add('filename', 'text', array(
            'required' => false,
        ))
        ->getForm();
    ;

    $form->bindRequest($request);

    if ($form->isValid()) {
        $paste = $form->getData();

        $id = $app['storage']->save($paste);

        return new RedirectResponse('/p/' . $id);
    }

    return $app['twig']->render('new.twig', array('form' => $form->createView()));
});

$app->get('/p/{id}', function($id) use ($app) {
    $paste = $app['storage']->get($id);

    if ($paste === false) {
        $app->abort(404, 'This paste does not exist yet.');
    }

    $view = $app['twig']->render('paste.twig', array(
        'paste' => $paste,
        // 'id'    => $id
        'id' => $app['storage']->getId($id),
    ));
    
    return new Response($view, 200, array(
        'Cache-Control' => 's-maxage=300',
    ));
})
->assert('id', '\d+');

$app->get('/p/{id}/raw', function($id) use ($app) {
    $paste = $app['storage']->get($id);

    return new Response($paste['paste'], 200, array(
        'Cache-Control' => 's-maxage=300',
        'Content-Type'  => 'text/plain',
    ));
})
->assert('id', '\d+');

$app->get('/p/{id}/download', function($id) use ($app) {
    $paste = $app['storage']->get($id);

    if (null == $filename = $paste['filename']) {
        $filename = 'paste-' . $id . '.txt';
    }

    return new Response($paste['paste'], 200, array(
        'Cache-Control' => 's-maxage=300',
        'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
    ));
})
->assert('id', '\d+');

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
