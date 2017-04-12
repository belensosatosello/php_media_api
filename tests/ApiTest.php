<?php

namespace Tests;

use Silex\WebTestCase;
use Silex\Application;

class ApiTest extends WebTestCase
{
    public function testInstagramLogin()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/');
        // Assert that the response is a redirect to the provided valid.login.url
        $this->assertTrue($client->getResponse()->isRedirect($this->app['valid.login_url']));
    }
    
    public function testInstagramSessionReset()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/');
        // Assert that the response is a redirect to the provided valid.login.url
        $this->assertTrue($client->getResponse()->isRedirect($this->app['valid.login_url']));
        // Save session token
        $this->app['session']->set('token', $this->app['invalid.token']);
        // Save session control value
        $this->app['session']->set('token_control', $this->app['invalid.token']);
        // Assert that the token is deleted and the control token exists
        $client = $this->createClient();
        $crawler = $client->request('GET', '/');
        $this->assertFalse($this->app['session']->has('token'));
        $this->assertTrue($this->app['session']->has('token_control'));
    }
    
    public function testProfilePage()
    {
        // Check for a valid token, user must submit a valid token to test
        // get a valid token from the following link
        //https://api.instagram.com/oauth/authorize?client_id=44904229b57445f49a88ef2de046379f&redirect_uri=http%3A%2F%2Flocalhost%3A8000%2F&response_type=token&state=01619ed&scope=basic+public_content
        //This will redirect to a url ending with access_token=XXX
        // paste XXX value it into config/test.json
        if ($this->app['valid.token']!='') {
            // Save session token
            $this->app['session']->set('token', $this->app['valid.token']);
            // Get the profile page
            $client = $this->createClient();
            $crawler = $client->request('GET', '/profile');
            // Assert that the response status code is 2xx
            $this->assertTrue($client->getResponse()->isSuccessful());
            // Assert that the response content contains a valid title
            $this->assertContains("full_name", $client->getResponse()->getContent());
        } else {
            echo "\n Get a valid token from: https://api.instagram.com/oauth/authorize?client_id=44904229b57445f49a88ef2de046379f&redirect_uri=http%3A%2F%2Flocalhost%3A8000%2F&response_type=token&state=01619ed&scope=basic+public_content";
            echo "\n This will redirect to a url ending with access_token=XXX \n";
            echo "paste XXX value into config/test.json";
        }
    }

    public function testUrlWithoutParams()
    {
        $client = $this->createClient();
        // test wrong url
        $crawler = $client->request('GET', '/media/');
        // Assert that the response status code is 404
        $this->assertTrue($client->getResponse()->isNotFound());
    }

    public function testValidMedia()
    {
        $client = $this->createClient();
        // test a valid image
        $this->app['session']->set('token', $this->app['valid.token']);
        $crawler = $client->request('GET', '/media/'.$this->app['valid.media_id']);
        // Assert that the "Content-Type" header is "application/json"
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
        // Assert that the response status code is 2xx
        $this->assertTrue($client->getResponse()->isSuccessful());
        // Assert that the response content contains the right latitude
        $this->assertContains(
            '"latitude":'.$this->app['valid.latitude'],
            $client->getResponse()->getContent()
        );
        // Assert that the response content contains the right longitude
        $this->assertContains(
            '"longitude":'.$this->app['valid.longitude'],
            $client->getResponse()->getContent()
        );
    }
    public function createApplication()
    {
        $env = "test";
        require __DIR__.'/../web/index.php';
        unset($app['exception_handler']);
        return $app;
    }
    
    public function testInvalidMedia()
    {
        $client = $this->createClient();
        //Set a valid token
        $this->app['session']->set('token', $this->app['valid.token']);
        // test an invalid image
        $crawler = $client->request('GET', '/media/'.$this->app['invalid.media_id']);
        // Assert that the "Content-Type" header is "application/json"
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            ),
            'Header'.$client->getResponse()
        );
        // Assert that the response content contains error type
        $this->assertContains('APINotFoundError', $client->getResponse()->getContent());
        // Assert that the response status code is 400
        $this->assertEquals(
            400,
            $client->getResponse()->getStatusCode()
        );
    }
    
    public function testNoLocation()
    {
        $client = $this->createClient();
        //Set a valid token
        $this->app['session']->set('token', $this->app['valid.token']);
        // test an invalid image
        $crawler = $client->request('GET', '/media/'.$this->app['invalid.no_location_media_id']);
        // Assert that the "Content-Type" header is "application/json"
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
        // Assert that the response content contains "no location data"
        $this->assertContains('no location data', $client->getResponse()->getContent());
        // Assert that the response status code is 2xx
        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}
