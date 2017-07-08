<?php

use Haridarshan\Instagram\Instagram;
use Haridarshan\Instagram\InstagramOAuth;
use LocationAPI\Repository\InstagramRepository;
use Silex\WebTestCase;

class InstagramRepositoryTest extends WebTestCase
{
    protected $instagramApiMock;

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
        $this->instagramApiMock = $this->getMockBuilder(Instagram::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testGetTokenOk()
    {
        $_GET['code'] = $this->app['valid.code'];

        $oauthMock = $this->getMockBuilder(InstagramOAuth::class)
            ->disableOriginalConstructor()
            ->getMock();

        $oauthMock->expects($this->once())
            ->method('getAccessToken')
            ->willReturn($this->app['valid.token']);

        $this->instagramApiMock->expects($this->once())
            ->method('oauth')
            ->willReturn($oauthMock);

        $instaRepo = new InstagramRepository($this->instagramApiMock);

        $result = $instaRepo->getToken();

        $this->assertNotNull($result);
        $this->assertEquals($this->app['valid.token'], $result);
    }

//    public function testGetUserDetailsOk()
//    {
//        $mockRequest = $this->getMockBuilder(InstagramRequest::class)
//            ->disableOriginalConstructor()
//            ->getMock();
//
//        $mockResponse = $this->getMockBuilder(InstagramResponse::class)
//            ->disableOriginalConstructor()
//            ->getMock();
//
//        $mockRequest->expects($this->once())
//            ->method('getResponse')
//            ->willReturn($mockResponse);
//
//        $mockUserData = json_encode($this->app['valid.userDetails']);
//
//        $mockResponse->expects($this->once())
//            ->method('getData')
//            ->willReturn($mockUserData);
//
//        $instaRepo = new InstagramRepository($this->instagramApiMock);
//
//        $result = $instaRepo->getUserDetails($this->app['valid.token']);
//
//        $this->assertNotNull($result);
//        $this->assertEquals($this->app['valid.token'], $result);
//    }
}
