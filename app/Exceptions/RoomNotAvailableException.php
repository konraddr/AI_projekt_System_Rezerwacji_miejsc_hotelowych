<?php

namespace App\Exceptions;

use Exception;

class RoomNotAvailableException extends Exception
{
    public function __construct()
    {
        parent::__construct('Wybrany termin jest już zajęty. Wybierz inne daty.');
    }
}
