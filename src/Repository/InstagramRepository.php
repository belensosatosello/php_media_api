<?php

namespace LocationAPI\Repository;

use Exception;
use Haridarshan\Instagram\Instagram;
use Haridarshan\Instagram\InstagramRequest;
use Haridarshan\Instagram\Exceptions\InstagramException;
use Silex\Application;

/**
 * Class InstagramRepository
 *
 * @package LocationAPI\Repository
 */
class InstagramRepository implements InstagramInterface
{

    /**
     * The Instagram object.
     *
     * @var Instagram
     */
    protected $instagram;

    /**
     * InstagramRepository constructor.
     *
     * @param $client_id
     * @param $clientSecret
     * @param $clientCallback
     * @param $state
     */
    public function __construct($client_id, $clientSecret, $clientCallback, $state)
    {
        $instagram = new Instagram(
            array(
            'ClientId' => $client_id,
            'ClientSecret' => $clientSecret,
            'Callback' => $clientCallback,
            "State" => $state,
            )
        );
        
        $this->instagram = $instagram;
    }
    
    /**
     * This function gets the token required to retrieve data from the Instagram API.
     *
     * @param Application $app  the Silex application object.
     * @return \Symfony\Component\HttpFoundation\RedirectResponse redirects to /profile
     *
     * @access public
     */
    public function setToken(Application $app)
    {
        $app['session']->remove('token');

        if (!isset($_GET['code'])) {
            $scope = [
                "basic",
                "public_content"
            ];
            return $app->redirect($this->instagram->getLoginUrl(["scope" => $scope]));
        } else {
            $oauth= $this->instagram->oauth($_GET['code']);
            $token = $oauth->getAccessToken();
            $app['session']->set('token', $token);
            $app['session']->set('oauth', $oauth);
            return $app->redirect('/profile');
        }
    }

    /**
     * This function retrieves basic information of the user that has been authenticated.
     *
     * @param Application $app the Silex application object.
     * @return \Symfony\Component\HttpFoundation\JsonResponse the json response with user data.
     *
     * @access public
     */
    public function getUserDetails(Application $app)
    {
        $token = $app['session']->get('token');

        try {
            $request = new InstagramRequest($this->instagram, "/users/self", [ "access_token" => $token ]);

            $response = $request->getResponse();
            $user_data = $response->getData();

            $user = [];

            if(!empty($user_data))
            {
                $user['full_name'] = $user_data->full_name;
                $user['username'] = $user_data->username;
            }

        } catch (InstagramResponseException $e) {
            echo $e->getMessage();
        } catch (InstagramServerException $e) {
            echo $e->getMessage();
        }
        return $app->json($user);
    }

    /**
     *	This function gets the media Location latitude and longitude.
     *
     * 	@param object $app the silex application object.
     * 	@param string $media_id the media id for which we are retrieving the location.
     *
     *	@return string $geopoint an array containing latitude and longitude.
     *
     *	@access public
     */
    public function getMediaLocation(Application $app, $media_id)
    {
        $token = $app['session']->get('token');

        try {
            $request = new InstagramRequest($this->instagram, "/media/".$media_id, [ "access_token" => $token ]);

            $response = $request->getResponse();

            $user_data = $response->getBody();

            //If the response was not successful, return the error code
            if (!($response) && $response->meta->code != 200) {
                return $app->json($response, $response->meta->code); //TODO: Corregir esto
            }
        } catch (InstagramException $e) {
            $app['monolog']->debug(sprintf("InstagramRepository::getMediaLocation - InstagramException is %s", $e->getMessage()));
            return $app->json($e->getType(), $e->getCode());
        } catch (Exception $ex) {
            $app['monolog']->debug(sprintf("InstagramRepository::getMediaLocation - Exception is %s", $ex->getMessage()));
            return $app->json($ex, 500);
        }

        $geopoint = array(
            'latitude' => 'no data found',
            'longitude'=> 'no data found'
        );

        try {
            if (empty($user_data->data->location)) {
                throw new Exception("No instagram location data was found.");
            }

            $geopoint['latitude'] = $user_data->data->location->latitude;
            $geopoint['longitude'] =$user_data->data->location->longitude;

        } catch (Exception $ex) {
            return $app->json(['id' => $user_data->data->id, 'location' => 'no location data']);
        }

        return json_encode($geopoint,JSON_UNESCAPED_UNICODE);
    }
}
