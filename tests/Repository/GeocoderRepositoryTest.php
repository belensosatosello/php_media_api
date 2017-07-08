<?php

use LocationAPI\Repository\GeocoderRepository;
use \Ivory\HttpAdapter\SocketHttpAdapter;
use \Geocoder\Provider\GoogleMaps;
use Silex\WebTestCase;

class GeocoderRepositoryTest extends WebTestCase
{
    protected $geocoderApiMock;

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

        $this->geocoderApiMock = $this->getMockBuilder(GoogleMaps::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testGetLocationDataOk()
    {
        $socketAdapterMock = $this->getMockBuilder(SocketHttpAdapter::class)
            ->getMock();

        $this->geocoderApiMock->expects($this->once())
            ->method('reverse')
            ->willReturn($this->app['valid.locationData']);

        $this->geocoderApiMock->expects($this->once())
            ->method('getBody')
            ->willReturn($this->app['valid.locationData']);

        $geocoderRepoMock = new GeocoderRepository($socketAdapterMock);

        $result = $geocoderRepoMock->getLocationData($this->app,$this->app['valid.latitude'],$this->app['valid.longitude']);

        $this->assertNotNull($result);


    }
}