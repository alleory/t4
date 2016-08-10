<?php

namespace T4\Validation\Validators;

use T4\Core\Validator;
use T4\Validation\Exceptions\InvalidEmail;

class Email extends Validator
{

    public function validate($value):bool
    {
        if ( false === filter_var($value, \FILTER_VALIDATE_EMAIL) ) {
            throw new InvalidEmail($value);
        }
        return true;
    }

}