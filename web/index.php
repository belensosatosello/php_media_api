<?php
use App\ApiController;
use Haridarshan\Instagram\Instagram;
use Haridarshan\Instagram\InstagramRequest;
use Haridarshan\Instagram\Exceptions\InstagramOAuthException;
use Haridarshan\Instagram\Exceptions\InstagramResponseException;
use Haridarshan\Instagram\Exceptions\InstagramServerException;
	
	
/**
	* handle static requests
*/
if (array_key_exists('REQUEST_URI', $_SERVER) &&
	preg_match('/\.(?:html|js|css|png|jpg|jpeg|gif|woff|ttf)$/', $_SERVER['REQUEST_URI'])) {
	return false;
}
	
// web/index.php
require_once __DIR__.'/../vendor/autoload.php';
	
$app = new Silex\Application();
$app->register(new Silex\Provider\SessionServiceProvider());
	
//Enable debug to see error details
$app['debug'] = true;
	
$app['instagram'] = function() use ($app){
	return new Instagram(
		array(
		'ClientId' => '44904229b57445f49a88ef2de046379f',
		'ClientSecret' => 'b4b4fc1b7a1540ac91c72eeeca6c3d47',
		'Callback' => 'http://localhost:8000/',
		)
	);
};

// ... definitions
$app-> get('/media/{media_id}', "App\ApiController::getMediaLocation");

// ... definitions
$app-> get('/', "App\ApiController::setToken");

$app-> get('/profile', "App\ApiController::getUser");
	
//Run silex
$app->run();