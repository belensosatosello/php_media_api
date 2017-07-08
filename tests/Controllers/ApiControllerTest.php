<?php

use LocationAPI\Controllers\ApiController;
use LocationAPI\Repository\GeocoderRepository;
use LocationAPI\Repository\InstagramRepository;
use Silex\WebTestCase;

class ApiControllerTest extends WebTestCase
{
    protected $instagramMock;
    protected $geocoderMock;

    public function createApplication()
    {
        $env = "test";
        require __DIR__ . '/../../web/index.php';
        $app['session.test'] = true;
        return $app;
    }

    public function setUp()
    {
        parent::setUp();
        $this->instagramMock = $this->getMockBuilder(InstagramRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->geocoderMock = $this->getMockBuilder(GeocoderRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testGetTokenOk()
    {
        $getTokenMockResponse = $this->app['valid.token'];

        $this->instagramMock->expects($this->once())
            ->method('getToken')
            ->will($this->returnValue($getTokenMockResponse));

        $apiController = new ApiController($this->instagramMock, $this->geocoderMock);

        $result = $apiController->getToken($this->app);

        $this->assertNotNull($result);
        $this->assertEquals($this->app['valid.token'], $this->app['session']->get('token'));
    }

    public function testGetUserOk()
    {
        $this->app['session']->set('token', $this->app['valid.token']);

        $getUserMockResponse = json_encode($this->app['valid.userDetails']);

        $this->instagramMock->expects($this->once())
            ->method('getUserDetails')
            ->will($this->returnValue($getUserMockResponse));

        $apiController = new ApiController($this->instagramMock, $this->geocoderMock);

        $result = $apiController->getUser($this->app);

        $this->assertNotNull($result);
        $this->assertEquals(200, json_decode($result)->meta->code);
        $this->assertContains("username", $result);
        $this->assertContains("full_name", $result);
    }

    public function testGetMediaLocationOk()
    {
        $getMediaLocationMockResponse = json_encode($this->app['valid.geopoint']);

        $this->instagramMock->expects($this->once())
            ->method('getMediaLocation')
            ->will($this->returnValue($getMediaLocationMockResponse));

        $getLocationDataMockResponse = $this->app['valid.locationData'];

        $this->geocoderMock->expects($this->once())
            ->method('getLocationData')
            ->willReturn($getLocationDataMockResponse);

        $this->app['monolog']->debug(sprintf("Get Media Location Mock Response is %s :",
            json_encode($getMediaLocationMockResponse)));

        $apiController = new ApiController($this->instagramMock, $this->geocoderMock);

        $result = $apiController->getMediaLocation($this->app, $this->app['valid.media_id']);

        $this->assertNotNull($result);
        $this->assertEquals(200, json_decode($result)->meta->code);
        $this->assertContains("geopoint", $result);
        $this->assertContains("street", $result);
        $this->assertContains("administrative_area_level_1", $result);
    }
}
