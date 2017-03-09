<?php
use App\ApiController;
use Haridarshan\Instagram\Instagram;
use Lokhman\Silex\Provider\ConfigServiceProvider;
	
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

//Load config file
$app->register(new ConfigServiceProvider(), [
    'config.dir' => __DIR__ . '/../config',
]);

$app['instagram'] = function() use ($app){
	return new Instagram(
		array(
		'ClientId' => $app['instagram_api']['clientId'],
		'ClientSecret' => $app['instagram_api']['clientSecret'],
		'Callback' => $app['instagram_api']['clientCallback'],
		)
	);
};

// This URL gets the media_id provided as a parameter and returns the location data in json format.
$app-> get('/media/{media_id}', "App\ApiController::getMediaLocation");

// This URL sets the token for the client.
$app-> get('/', "App\ApiController::setToken");

// This URL shows basic information from the user that has been authenticated
$app-> get('/profile', "App\ApiController::getUser");
	
//Run silex
$app->run();