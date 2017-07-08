<?php

namespace LocationAPI\Repository;

use Silex\Application;

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
    public function getLocationData(Application $app,$latitude, $longitude);
}
