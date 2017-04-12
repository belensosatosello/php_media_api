<?php

namespace LocationAPI\Repository;

/**
 * Interface GeocoderInterface
 *
 * @package LocationAPI\Repository
 */
interface GeocoderInterface
{
    /**
     * @param $latitude
     * @param $longitude
     * @return mixed
     */
    public function getLocationData($latitude, $longitude);
}
