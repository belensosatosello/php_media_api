<?php

namespace LocationAPI\Exception;

class DataNotAvailable extends \Exception
{
    public function __construct(\Exception $previous = null)
    {
        parent::__construct("Location data not available", 404, $previous);
    }
}
