<?php

namespace App\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

class EmailAvailableEditException extends ValidationException{

    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'Email already taken',
        ],
    ];
}