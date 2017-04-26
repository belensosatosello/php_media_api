<?php

namespace LocationAPI\Repository;

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
    public function __construct()
    {
        $curl = new \Ivory\HttpAdapter\SocketHttpAdapter();
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
    public function getLocationData($latitude, $longitude)
    {
        $response = array(
            'street' => null,
            'administrative_area_level_1'=> null,
            'administrative_area_level_2'=> null,
            'country'=>null
        );

        $results =$this->geocoder->reverse($latitude, $longitude);

        if (!empty($data = $results->first())) {
            $admin_levels = $data->getAdminLevels();

            $response = array(
                'street' => $data->getStreetName(),
                'administrative_area_level_1'=> $admin_levels->get(1)->getName(),
                'administrative_area_level_2'=> $admin_levels->get(2)->getName(),
                'country'=>$data->getCountry()->getName()
            );
        }

        return $response;
    }
}
