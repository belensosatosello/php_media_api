<?php

namespace LocationAPI\Repository;

use Haridarshan\Instagram\Exceptions\InstagramException;
use Haridarshan\Instagram\Exceptions\InstagramResponseException;
use Haridarshan\Instagram\Instagram;
use Haridarshan\Instagram\InstagramRequest;

/**
 * Class InstagramRepository
 *
 * @package LocationAPI\Repository
 */
class InstagramRepository implements InstagramInterface
{
    const ERROR_TOKEN = "User has not been identified.";
    const ERROR_MSG_TOKEN = "API requires an authenticated users access token";

    /**
     * The Instagram object.
     *
     * @var Instagram
     */
    protected $instagram;

    /**
     * InstagramRepository constructor.
     *
     * @param $instagram
     */
    public function __construct(Instagram $instagram)
    {
        $this->instagram = $instagram;
    }

    /**
     * This function gets the token required to retrieve data from the Instagram API.
     *
     * @return string access token
     *
     * @access public
     */
    public function getToken()
    {
        if (!isset($_GET['code'])) {
            return null;
        } else {
            $oauth = $this->instagram->oauth($_GET['code']);
            $token = $oauth->getAccessToken();

            return $token;
        }
    }

    /**
     * This function gets the login Url using Instagram's library method.
     *
     * @param array scope of the permission that will be granted
     * @return string the URL to login
     * @access public
     */
    public function getLoginUrl($scope)
    {
        return $this->instagram->getLoginUrl($scope);
    }

    /**
     * This function retrieves basic information of the user that has been authenticated.
     *
     * @param $token the an access token
     * @return the json response with user data.
     *
     * @access public
     * @throws InstagramException
     */
    public function getUserDetails($token)
    {
        if (!$token) {
            $message = json_encode(array(
                'Type' => "Unauthorized",
                'Message' => "API requires an authenticated users access token"
            ));

            throw new InstagramException($message, 401);
        }

        try {
            $request = new InstagramRequest($this->instagram, "/users/self", ["access_token" => $token]);

            $response = $request->getResponse();
            $user_data = $response->getData();

            $user = [];

            if (!empty($user_data)) {
                $user['meta'] = array(
                    "code" => '200'
                );

                $user['full_name'] = $user_data->full_name;
                $user['username'] = $user_data->username;
            }
        } catch (InstagramResponseException $e) {
            throw new InstagramException($e->getMessage(), $e->getCode());
        } catch (InstagramServerException $e) {
            throw new InstagramException($e->getMessage(), $e->getCode());
        }
        return json_encode($user);
    }

    /**
     *    This function gets the media Location latitude and longitude.
     *
     * @param string $token an access token.
     * @param string $media_id the media id for which we are retrieving the location.
     *
     * @return string $geopoint an array containing latitude and longitude.
     *
     * @access public
     * @throws InstagramException
     */
    public function getMediaLocation($token, $media_id)
    {
        if (!$token) {
            $message = json_encode(array(
                'Type' => "Unauthorized",
                'Message' => "API requires an authenticated users access token"
            ));
            throw new InstagramException($message, 401);
        }

        try {

            //TODO : Instagram may be unreachable (disconeccted from internet or whatever)
            $request = new InstagramRequest($this->instagram, "/media/" . $media_id, ["access_token" => $token]);

            $response = $request->getResponse();

            $user_data = $response->getBody();

            $geopoint = [];

            if (empty($user_data->data->location)) {
                $msg = json_encode(array('Type' => 'APINotFoundError', 'Message' => 'Location data not available'));
                throw new InstagramException($msg, 400);
            }

            $geopoint['latitude'] = $user_data->data->location->latitude;
            $geopoint['longitude'] = $user_data->data->location->longitude;
        } catch (InstagramResponseException $e) {
            throw new InstagramException($e->getMessage(), $e->getCode());
        } catch (InstagramServerException $e) {
            throw new InstagramException($e->getMessage(), $e->getCode());
        }

        return json_encode($geopoint, JSON_UNESCAPED_UNICODE);
    }
}
