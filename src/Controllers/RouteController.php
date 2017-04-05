<?php

namespace Controllers;

use Symfony\Component\HttpFoundation\Request;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
 
class RouteController implements ControllerProviderInterface
{
	public function connect(Application $app) {
	$factory=$app['controllers_factory'];
	
	// This URL gets the media_id provided as a parameter and returns the location data in json format.
	$factory-> get('/media/{media_id}', "Controllers\ApiController::getMediaLocation");
	// This URL sets the token for the client.
	$factory-> get('/', "Controllers\ApiController::setToken");
	// This URL shows basic information from the user that has been authenticated
	$factory-> get('/profile', "Controllers\ApiController::getUser");
	
	return $factory;

	}
}