<?php

namespace App\Entity\Exception;

use \InvalidArgumentException;

class UserInvalidArgumentException extends InvalidArgumentException
{
    public function __construct($message, $code = 400)
    {
        parent::__construct($message, $code);
    }

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
