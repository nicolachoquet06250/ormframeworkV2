<?php

namespace ormframework\core\errors;

class http_error extends \ormframework\core\db_context\entity
{
    public $code, $message = 'Page not found';

    public function header() {
        header('HTTP/1.0 '.(string)$this->code.' '.$this->message);
    }
}