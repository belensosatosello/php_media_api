<?php

// /app/app.php
require __DIR__.'/bootstrap.php';

use Haridarshan\Instagram\Instagram;
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

$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__.'/development.log',
));

if (!isset($env)) {
    $env = 'dev';
}

//Load config file
$app->register(new ConfigServiceProvider(), [
    'config.dir' => __DIR__ . '/../config',
	'config.env' => $env,
]);

$app['instagram'] = function() use ($app){
	return new Instagram(
		array(
		'ClientId' => $app['instagram_api']['clientId'],
		'ClientSecret' => $app['instagram_api']['clientSecret'],
		'Callback' => $app['instagram_api']['clientCallback'],
		"State" => $app['instagram_api']['state'],
		)
	);
};

// Load Routes
$app->mount('/', new Controllers\RouteController());
	
return $app;