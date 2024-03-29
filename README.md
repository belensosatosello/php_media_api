# **Media Location API**

This Rest API returns information about the location of an Instagram media. This information can be retrieved by hitting the endpoint provided using the media id as a parameter.

* * *
## **Setup**

To run this project you need:

- [PHP 7.1](http://windows.php.net/download#php-7.1) (the following extensions need to be enabled: php\_curl.dll, php\_mbstring.dll,php\_openssl.dll)
- [Curl](https://gist.github.com/VersatilityWerks/5719158/download)
- [Silex](http://silex.sensiolabs.org/)
- [Composer](https://getcomposer.org/)

To enable Curl download the file from the link above and save it in:

    {yourPathToPHPInstallFolder}\ext

Then uncomment and edit the following line in the php.ini file:

    curl.cainfo="{yourPathToPHPInstallFolder}\ext\cacert.pem"

After that you will need to run the following:

    $ git clone https://belensosatosello@bitbucket.org/belensosatosello/belensosatosello-test.git
    $ cd belensosatosello-test
    $ composer self-update
    $ composer install
    $ php -S localhost:8000 -t web web/index.php

Now you can access the following URLs:

- [localhost:8000](http://localhost:8000): You must access this url the first time you use the API. It will take you to the Instagram login page and request for permissions to your Instagram account.Your token will be generated and you will be redirected to /profile.
- [localhost:8000/profile](http://localhost:8000/profile): Here you can see basic information of the authenticated user.
- [localhost:8000/media/{media\_id}](http://localhost:8000/media/1402451097368744018_1173952339): Here you can see the media location data.

* * *
### Endpoint

    localhost:8000/media/{media_id}

### Sample Request
    GET /media/123456


### Sample Response  
    {
        "meta":{
            "code":"200"
            },
        "id":"123456",
        "location":{
            "geopoint":{
                "latitude":42.277,
                "longitude":-71.9256
            },
            "street":"Fake Street",
            "administrative_area_level_1":"Any City",
            "administrative_area_level_2":"Example State",
            "country":"Example Country"
        }
    }


* * *
## **Run Unit Tests**

To run unit test, execute the following command on the project root directory:

    vendor\bin\phpunit --configuration phpunit.xml.dist

In order to run unit tests you will need to get a valid token from the following URL:
    
	https://api.instagram.com/oauth/authorize?client_id=44904229b57445f49a88ef2de046379f&redirect_uri=http%3A%2F%2Flocalhost%3A8000%2F&response_type=token&state=01619ed&scope=basic+public_content

This will redirect to a url ending with access_token=XXX,  paste XXX value it into config/test.json

* * *
## **Development Explanation**
I chose [Bitbucket](https://bitbucket.org/) as a version control system since it was one of the few that provide private repositories for free. 

This project was built using Silex as suggested. This microframework turned out to be really easy to use and helpful to handle routing.  

This API it was tested using built in PHP server due to the fact that it was the faster and simpler way to do it (There was no need of extra software). 

I used [Instagram-php](https://github.com/haridarshan/instagram-php) to get Instagram token and data. This library is a wrapper that simplifies the access to Instagram API. I struggled a bit trying to get the token within the application but this library helped me to do it.

I also struggled when developing and running the Unit Tests, but following other sample applications (as well as development blogs and forums on Silex and PHPUnit) I was able to figure out what to test and how.

After the **code review** some changes were introduced. 

* Composer.lock was removed from .gitignore to include it in the repository.
* A class was implemented to provide routes instead of using a "require". This was done following [this](https://www.matthewdawkins.co.uk/2015/05/controller-routes-in-silex/) tutorial.
* [This](https://bitbucket.org/belensosatosello/belensosatosello-test/src/559c8ec06c0a58de91ed7f6cb008228de3710970/app/app.php?at=master&fileviewer=file-view-default#app.php-13:16) piece of code was improved using [this](http://symfony.com/doc/current/introduction/http_fundamentals.html#requests-and-responses-in-symfony).
* Dependency injection was added follwing [Silex Documentation](https://silex.sensiolabs.org/doc/2.0/providers/service_controller.html).

After completing these steps I downloaded PHP Storm which improved the detection of coding errors. This IDE also helped to use php-cs-fixer (with a plugin) to make the code follow PSR-2 standards.

Another important change was the use of namespaces and the implementation of the [Repository Pattern](http://designpatternsphp.readthedocs.io/en/latest/More/Repository/README.html).

Unit Tests were updated in order to mock external services, this helped to identify structure errors in the project.

Regarding error handling it was really hard for me to found a standard to follow as best practice. I worked with this tutorial which looked like a good approach. 


 