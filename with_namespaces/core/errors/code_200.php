<?php

namespace ormframework\core\errors;

class code_200 extends http_error
{
    public $code = 200, $message = 'Get page successful';
}