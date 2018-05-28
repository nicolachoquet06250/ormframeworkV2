<?php

class router
{
    public $routes = [
        'index.htm' => 'index.html',
        'voici_ma_doc.htm'   => 'doc/index.html'
    ];

    public function route($url) {
        if(isset($this->routes[$url]) || count(explode('.', $url)) > 1) {
            if(isset($this->routes[$url])) {
                if (is_file('custom/website/' . $this->routes[$url])) {
                    $type = 'custom';
                    echo file_get_contents($type.'/website/'.$this->routes[$url]);
                } elseif (is_file('core/website/' . $this->routes[$url])) {
                    $type = 'core';
                    echo file_get_contents($type.'/website/'.$this->routes[$url]);
                } else {
                    $type = 'core';

                    ${404} = error_manager::instence()->http_error();
                    ${404}->code = 404;
                    ${404}->header();

                    if(is_file('custom/website/errors/404.html')) {
                        $type = 'custom';
                    }
                    elseif (is_file('core/website/errors/404_.php')) {
                        $type = 'core';
                    }
                    echo file_get_contents($type.'/website/errors/404.html');
                }
            }
            else {
                $type = 'core';

                ${404} = error_manager::instence()->http_error();
                ${404}->code = 404;
                ${404}->header();

                if(is_file('./custom/website/errors/404.html')) {
                    $type = 'custom';
                }
                elseif (is_file('./core/website/errors/404.html')) {
                    $type = 'core';
                }

                echo file_get_contents('./'.$type.'/website/errors/404.html');
            }
        }
        else {
            Main::instence(utils::http_get('path'));
        }
    }
}