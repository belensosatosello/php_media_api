<?php

namespace LocationAPI\Exception;

class UserNotFound extends \Exception
{
    public function __construct(\Exception $previous = null)
    {
        parent::__construct("API requires an authenticated users access token", 401, $previous);
    }
}
