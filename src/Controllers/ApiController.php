<?php

namespace Controllers;
	
use Silex\Application;
use Exception;
use Haridarshan\Instagram\Instagram;
use Haridarshan\Instagram\InstagramRequest;
use Haridarshan\Instagram\Exceptions\InstagramException;
	
class ApiController
{
	public function getMediaLocation(Application $app,$media_id)
	{		
		$instagram = $app['instagram'];
		$token = $app['session']->get('token');
		
		try {
			// To get User Profile Details or to make any api call to instagram
			$user = new InstagramRequest($instagram, "/media/".$media_id, [ "access_token" => $token ]);
			// To get Response
			$user_response = $user->getResponse();
			
			// To get Body
			$user_data = $user_response->getBody();
			
			//If the response was not successful, return the error code
			if($user_data->meta->code != 200)
			{
				return $app->json($response,$response->meta->code);
			}

		}catch(InstagramException $e) {
			return $app->json($e->getType() , $e->getCode());
		} catch (Exception $ex) {
            return $app->json($ex, 500);
        }
	
		$geopoint = [];
	
		try{
			if(empty($user_data->data->location))
			{
				throw new Exception("No instagram location data was found.");
			}	
			//Get the location information
			$geopoint['latitude'] = $user_data->data->location->latitude;
			$geopoint['longitude'] =$user_data->data->location->longitude;
			
			
		} catch (Exception $ex) {
            return $app->json(['id' => $user_data->data->id, 'location' => 'no location data']);
        }
		
		$location = array(
			"geopoint"=>$geopoint,
			);
			
		$result_array = array(
			"id" => $media_id,
			"location" => $location,
		);
		
		return $app->json($result_array);
	
	}
	
		
	/**
	*	This function gets the token required to retrieve data from the Instagram API.
	*	
	*	@param object $app the silex application object.
	*	
	* 	@return redirects to /profile.
	*
	*	@access public
	*/
	public function setToken(Application $app)
	{
		$app['session']->remove('token');
		$instagram = $app['instagram'];
			
		// If we don't have an authorization code then get one
		if (!isset($_GET['code'])) {
			$scope = [
				"basic",
				"public_content"
			];
			return $app->redirect($instagram->getLoginUrl(["scope" => $scope]));
		} else {
			$oauth= $instagram->oauth($_GET['code']);
			$token = $oauth->getAccessToken();
			$app['session']->set('token', $token);
			$app['session']->set('oauth', $oauth);
			return $app->redirect('/profile');
		}
	}
		
	/**
	*	This function shows basic information of the user that has been authenticated.
	*
	* 	@param object $app the silex application object.
	*	
	*	@return string the json response with user data.
	*
	*	@access public
	*/
	public function getUser(Application $app)
	{
		$instagram = $app['instagram'];
		$token = $app['session']->get('token');
		
		try {
			// To get User Profile Details or to make any api call to instagram
			$request = new InstagramRequest($instagram, "/users/self", [ "access_token" => $token ]);
			// To get Response
			$user_response = $request->getResponse();
			// To get Body
			$user_data = $user_response->getData();
			
			$user = [];
			$user['full_name'] = $user_data->full_name;
			$user['username'] = $user_data->username;
		
		} catch(InstagramResponseException $e) {
			echo $e->getMessage();
		}catch(InstagramServerException $e) {
			echo $e->getMessage();
		}	
			return $app->json($user);
		}
	}