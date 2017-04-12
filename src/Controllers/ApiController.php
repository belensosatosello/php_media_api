<?php

namespace LocationAPI\Controllers;

use LocationAPI\Repository\GeocoderRepository;
use LocationAPI\Repository\InstagramRepository;
use Silex\Application;

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
     * @var $instagram LocationAPI\Repository\InstagramRepository;
     */
    protected $instagram;

    /**
     *  The GeocoderRepository where Geocoding data is persisted.
     *
     * @var $instagram LocationAPI\Repository\GeocoderRepository;
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
        $geopoint = $this->instagram->getMediaLocation($app, $media_id);
        $app['monolog']->debug(sprintf("ApiController::getMediaLocation geopoint is %s", $geopoint));

        $geopoint = json_decode($geopoint);

        if ((!empty($geopoint))) {
            $extra_data = $this->geocoder->getLocationData($geopoint->latitude, $geopoint->longitude);
        }

        $location = [];

        $location['geopoint'] =$geopoint;

        if(!empty($extra_data))
        {
            $location['street'] = $extra_data['street'];
            $location['administrative_area_level_1'] = $extra_data['administrative_area_level_1'];
            $location['administrative_area_level_2'] = $extra_data['administrative_area_level_2'];
            $location['country'] = $extra_data['country'];
        }


        $result_array = array(
            "id" => $media_id,
            "location" => $location
        );

        return json_encode($result_array,JSON_UNESCAPED_UNICODE);
    }

    /**
     * This function gets the token required to retrieve data from the Instagram API.
     *
     * @param object|Application $app the silex application object.
     * @return redirects to /profile.
     *
     * @access public
     */
    public function setToken(Application $app)
    {
        return $this->instagram->setToken($app);
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
        return $this->instagram->getUserDetails($app);
    }
}
