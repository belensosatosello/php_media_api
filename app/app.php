<?php

// /app/app.php
require __DIR__ . '/bootstrap.php';

use Haridarshan\Instagram\Instagram;
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
    $instagram = new Instagram(
        array(
            'ClientId' => $app['instagram_api']['clientId'],
            'ClientSecret' => $app['instagram_api']['clientSecret'],
            'Callback' => $app['instagram_api']['clientCallback'],
            "State" => $app['instagram_api']['state'],
        )
    );

    return new Repository\InstagramRepository($instagram);
};

$app['geocoder.repository'] = function ($app) {
    return new Repository\GeocoderRepository();
};

//Register controller
$app['instagram.controller'] = function ($app) {
    return new Controllers\ApiController($app['instagram.repository'], $app['geocoder.repository']);
};

// Load Routes
$app->mount('/', new Controllers\RouteController());

return $app;
