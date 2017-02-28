<?php
namespace App;

use Silex\Application;
	
class ApiController
{
	public function getMediaLocation(Application $app,$media_id)
	{
		//Sample Media ID
		//$media_id = '1402451097368744018_1173952339';
		
		//Define access_token
		$access_token='1173952339.4490422.343e3f74bc6a48e9ac3541784d5af621';
		
		
		$instagram_url ="http://api.instagram.com/v1/media/".$media_id."?access_token=".$access_token;
			
		//Get the information from instagram
		$media_json = file_get_contents($instagram_url);
			
		if(!empty($media_json))
		{
			//Turn this information into a PHP array
			$media_array = json_decode($media_json, true);
			
			//Get the location information
			$lat = $media_array['data']['location']['latitude'];
			$long = $media_array['data']['location']['longitude'];
			
			$geopoint = array(
			"geopoint" => array(
			"latitude" => $lat,
			"longitude"=> $long,
			)
			);
			
			$result_array = array(
			"id" => $media_id,
			"location" => $geopoint
			);
			
		}
			
		return $app->json($result_array);
	}		
}
?>