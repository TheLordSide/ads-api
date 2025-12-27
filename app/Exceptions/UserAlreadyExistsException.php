<?php

namespace App\Exceptions;

use DomainException;

class UserAlreadyExistsException extends DomainException
{
    protected $message = 'A user with this email address already exists.';
}
