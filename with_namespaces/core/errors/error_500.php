<?php

namespace ormframework\core\errors;

class error_500 extends http_error
{
    public $code = 500, $message = 'erreur de serveur interne';
}