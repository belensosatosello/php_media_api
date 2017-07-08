<?php

namespace LocationAPI\Controllers;

use LocationAPI\Repository\GeocoderRepository;
use LocationAPI\Repository\InstagramRepository;
use Silex\Application;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class ApiController
 *
 * @package LocationAPI\Controllers
 */
class ApiController
{
    /**
     *  The InstagramRepository where instagram data is persisted.
     *
     * @var $instagram InstagramRepository;
     */
    protected $instagram;

    /**
     *  The GeocoderRepository where Geocoding data is persisted.
     *
     * @var $geocoder GeocoderRepository;
     *
     * @access protected
     */
    protected $geocoder;

    /**
     *  ApiController Constructor
     *
     * @param InstagramRepository $instagram
     * @param GeocoderRepository $geocoder
     */
    public function __construct(InstagramRepository $instagram, GeocoderRepository $geocoder)
    {
        $this->instagram = $instagram;
        $this->geocoder = $geocoder;
    }

    /**
     * This function gets the media location information including:
     * - latitude & longitude
     * - street
     * - administrative_area_level_1
     * - administrative_area_level_2
     * - country
     *
     * @param Application $app the silex Application Object
     * @param string $media_id the media id for which we are retrieving the location.
     * @return string with the json formatted information.
     *
     * @access public
     */
    public function getMediaLocation(Application $app, $media_id)
    {
        $token = $app['session']->get('token');

        $geopoint = $this->instagram->getMediaLocation($token, $media_id);

        $geopoint = json_decode($geopoint);

        if (!empty($geopoint->latitude) && !empty($geopoint->longitude)) {
            $extra_data = $this->geocoder->getLocationData($app,$geopoint->latitude, $geopoint->longitude);
        }

        $location = [];

        $location['geopoint'] = $geopoint;

        if (!empty($extra_data)) {
            $location['street'] = $extra_data['street'];
            $location['administrative_area_level_1'] = $extra_data['administrative_area_level_1'];
            $location['administrative_area_level_2'] = $extra_data['administrative_area_level_2'];
            $location['country'] = $extra_data['country'];
        }

        $result_array = array(
            "meta" => array(
                "code" => 200
            ),
            "id" => $media_id,
            "location" => $location
        );

        return json_encode($result_array, JSON_UNESCAPED_UNICODE);
    }

    /**
     * This function gets the token required to retrieve data from the Instagram API.
     *
     * @param Application $app the silex application object.
     * @return RedirectResponse to /profile or instagram login URL
     *
     * @access public
     */
    public function getToken(Application $app)
    {
        $app['session']->remove('token');

        $token = $this->instagram->getToken();

        if (!$token) {
            $scope = [
                "basic",
                "public_content"
            ];
            return $app->redirect($this->instagram->getLoginUrl(["scope" => $scope]));
        } else {
            $app['session']->set('token', $token);
            return $app->redirect('/profile');
        }
    }

    /**
     * This function shows basic information of the user that has been authenticated.
     *
     * @param Application $app the silex application object.
     * @return string the json response with user data.
     *
     * @access public
     */
    public function getUser(Application $app)
    {
        $token = $app['session']->get('token');

        return $this->instagram->getUserDetails($token);
    }
}
