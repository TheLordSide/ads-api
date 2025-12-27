<?php

namespace App\Exceptions;

use DomainException;

class InvalidCredentialsException extends DomainException
{
    protected $message = 'invalid credentials.';
}
