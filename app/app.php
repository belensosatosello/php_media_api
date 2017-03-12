<?php

// /app/app.php
require __DIR__.'/bootstrap.php';

use Controllers\ApiController;
use Haridarshan\Instagram\Instagram;
use Lokhman\Silex\Provider\ConfigServiceProvider;
	
/**
*  handle static requests
*/
if (array_key_exists('REQUEST_URI', $_SERVER) &&
	preg_match('/\.(?:html|js|css|png|jpg|jpeg|gif|woff|ttf)$/', $_SERVER['REQUEST_URI'])) {
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

// Routes
require __DIR__.'/routes.php';
	
return $app;