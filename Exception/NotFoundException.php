<?php


namespace app\Machine\Exception;


use Exception;
use Throwable;

class NotFoundException extends Exception
{

    protected $message = NOT_FOUND;
    protected $code = 404;

}