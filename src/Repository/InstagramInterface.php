<?php

namespace LocationAPI\Repository;

use Silex\Application;

/**
 * Interface InstagramInterface
 *
 * @package LocationAPI\Repository
 */
interface InstagramInterface
{
    /**
     * @param Application $app
     * @param $media_id
     * @return mixed
     *
     * @access public
     */
    public function getMediaLocation(Application $app, $media_id);

    /**
     * @param Application $app
     * @return mixed
     *
     * @access public
     */
    public function setToken(Application $app);

    /**
     * @param Application $app
     * @return mixed
     *
     * @access public
     */
    public function getUserDetails(Application $app);
}
