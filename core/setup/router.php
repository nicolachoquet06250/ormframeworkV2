<?php

namespace ormframework\core\setup;

use \ormframework\core\annotation\PhpDocParser;
use \ormframework\core\setup\utils;

class router extends utils
{
    private $routes;
    private $server;
    private $namespace = '\\ormframework\\custom\\annotations\\';

    public function __construct()
    {
        $this->server = $_SERVER['HTTP_HOST'];
        $this->server .= '/'.basename(json_decode(file_get_contents('./core/ormf-modules-conf.json'))->project_directory);
    }

    public function get_defaults_routes()
    {
        $this->routes = json_decode(file_get_contents('custom/router.json'), true);
        return $this;
    }

    /**
     * @return router
     */
    public static function instence() {
    	return new router();
	}

    /**
     * @param string $url
     * @return router
     */
    public function route($url) {
        foreach (PhpDocParser::instence()->parsing[$this->namespace.'route'] as $model => $routes) {
            foreach ($routes as $alias => $route) {
                $this->routes[$alias] = $route;
            }
        }

        if(count(explode('.', $url)) > 1) {
            if(isset($this->routes[$url])) {
                if (is_file('custom/website/' . $this->routes[$url])) {
                    $type = 'custom';
                    echo file_get_contents($type.'/website/'.$this->routes[$url]);
                } elseif (is_file('core/website/' . $this->routes[$url])) {
                    $type = 'core';
                    echo file_get_contents($type.'/website/'.$this->routes[$url]);
                } else {
                    $type = 'core';

                    ${404} = $this->get_manager('error')->http_error();
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

                ${404} = $this->get_manager()->error()->http_error();
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
            if (isset($this->routes[$url])) {
                header("Status: 301 Moved Permanently", false, 301);
                header("Location: http://{$this->server}/rest/{$this->routes[$url]}");
            } else {
                main::instence(utils::http_get('path'));
            }
        }
        return $this;
    }
}