<?php

// This URL gets the media_id provided as a parameter and returns the location data in json format.
$app-> get('/media/{media_id}', "Controllers\ApiController::getMediaLocation");

// This URL sets the token for the client.
$app-> get('/', "Controllers\ApiController::setToken");

// This URL shows basic information from the user that has been authenticated
$app-> get('/profile', "Controllers\ApiController::getUser");