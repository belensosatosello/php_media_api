<?php

namespace LocationAPI\Repository;

/**
 * Interface InstagramInterface
 *
 * @package LocationAPI\Repository
 */
interface InstagramInterface
{
    /**
     * @param $token
     * @param $media_id
     * @return mixed
     *
     * @access public
     */
    public function getMediaLocation($token, $media_id);

    /**
     * @return mixed
     *
     * @access public
     */
    public function getToken();

    /**
     * @param $token
     * @return mixed
     *
     * @access public
     */
    public function getUserDetails($token);
}
