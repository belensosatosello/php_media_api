<?php

namespace LocationAPI\Repository;

use Silex\Application;

/**
 * Class GeocoderRepository
 *
 * @package LocationAPI\Repository
 */
class GeocoderRepository implements GeocoderInterface
{
    /**
     * Geocoder(GoogleMaps) object.
     *
     * @var \Geocoder\Provider\GoogleMaps
     */
    protected $geocoder;

    /**
     * GeocoderRepository constructor.
     */
    public function __construct($curl)
    {
        $this->geocoder = new \Geocoder\Provider\GoogleMaps($curl);
    }

    /**
     * This function gets the location data for a provided latitude and longitude.
     *
     * It retrieves information form GoogleMaps API.
     *
     * @param $latitude
     * @param $longitude
     * @return array
     */
    public function getLocationData(Application $app, $latitude, $longitude)
    {
        $response = [
            'street' => "no data available",
            'administrative_area_level_1' => "no data available",
            'administrative_area_level_2' => "no data available",
            'country' => "no data available"
        ];

        $results = $this->geocoder->reverse($latitude, $longitude);

        $app['monolog']->debug(sprintf('The GEOCODER results %s',json_encode($results)));

        if (!empty($data = $results->first())) {
            $admin_levels = $data->getAdminLevels();

            if (!empty($data->getStreetName())) {
                $response['street'] = $data->getStreetName();
            }
            if (!empty($admin_levels->get(1))) {
                $response['administrative_area_level_1'] = $admin_levels->get(1)->getName();
            }
            if (!empty($admin_levels->get(2))) {
                $response['administrative_area_level_2'] = $admin_levels->get(2)->getName();
            }
            if (!empty($data->getCountry())) {
                $response['country'] = $data->getCountry()->getName();
            }
        }

        return $response;
    }
}
