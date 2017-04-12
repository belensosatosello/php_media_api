<?php

// /app/app.php
require __DIR__ . '/bootstrap.php';


use LocationAPI\Controllers;
use LocationAPI\Repository;
use Lokhman\Silex\Provider\ConfigServiceProvider;
use Symfony\Component\HttpFoundation\Request;

/**
 *  handle static requests
 */
$request = Request::createFromGlobals();
$requestURI = $request->server->get('REQUEST_URI');

if (preg_match('/\.(?:html|js|css|png|jpg|jpeg|gif|woff|ttf)$/', $requestURI)) {
    return false;
}

$app = new Silex\Application();

$app->register(new Silex\Provider\SessionServiceProvider());

$app->register(new Silex\Provider\ServiceControllerServiceProvider());

$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__ . '/../development.log',
));

if (!isset($env)) {
    $env = 'dev';
}

//Load configuration file
$app->register(new ConfigServiceProvider(), [
    'config.dir' => __DIR__ . '/../config',
    'config.env' => $env,
]);


//Register repositories
$app['instagram.repository'] = function ($app) {
    return new Repository\InstagramRepository(
        $app['instagram_api']['clientId'],
        $app['instagram_api']['clientSecret'],
        $app['instagram_api']['clientCallback'],
        $app['instagram_api']['state']
    );
};

$app['geocoder.repository'] = function ($app) {
    return new Repository\GeocoderRepository();
};

//Register controller
$app['instagram.controller'] = function ($app) {
    return new Controllers\ApiController($app['instagram.repository'], $app['geocoder.repository']);
};

//Register controller
$app['geocoder.controller'] = function ($app) {
    return new Controllers\GeocodeController();
};

// Load Routes
$app->mount('/', new Controllers\RouteController());

return $app;
