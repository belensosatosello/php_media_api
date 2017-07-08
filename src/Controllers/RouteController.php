<?php

namespace LocationAPI\Controllers;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;

/**
 * Class RouteController
 *
 * @package LocationAPI\Controllers
 */
class RouteController implements ControllerProviderInterface
{
    /**
     * This function returns the routes to connect to the given application.
     *
     * @param Application $app the silex Application Object
     * @return mixed
     */
    public function connect(Application $app)
    {
        $factory = $app['controllers_factory'];

        $factory->get('/media/{media_id}', "instagram.controller:getMediaLocation");
        $factory->get('/', "instagram.controller:getToken");
        $factory->get('/profile', "instagram.controller:getUser");

        return $factory;
    }
}
