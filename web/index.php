<?php
	use App\ApiController;
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
	
	//Enable debug to see error details
	$app['debug'] = true;
	
	// ... definitions
	$app-> get('/media/{media_id}', "App\ApiController::getMediaLocation");
	
	//Run silex
	$app->run();

?>