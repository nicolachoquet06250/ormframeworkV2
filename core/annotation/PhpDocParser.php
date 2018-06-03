<?php

require_once '../services/interfaces/service.php';

class PhpDocParser implements service
{
    private $commentaires = [];

    public function __construct($file)
    {
        $regex_class_model = '`[\<\?a-zA-Z\t\n]+class\ ([a-zA-Z0-9\_]+)[\.\ a-zA-Z0-9\{\n\t\/\*\@é\\\'\$\_\(\)\ =\;\-\>\}]+`';
        $regex_1 = '`\/\*\*[\n\t\ ]+([\n\t\ \*\@a-zA-Zé\\\'\$\-\?\_\.\/ 0-9]+)[\n\t\ ]+ \*\*\/`';

        $class = '';
        $commentaires = [];

        $content = file_get_contents($file);

        //récupération du nom de model et controller
        preg_replace_callback($regex_class_model, function ($matches) use (&$class) {
            $class = str_replace('_model', '', $matches[1]);
        }, $content);

        //récupération d'un tableau d'information sur chaque méthode
        preg_replace_callback($regex_1, function ($matches) use (&$commentaires) {
            $commentaires[] = $matches[1];
        }, $content);

        // nétoyage du tableau récupéré
        foreach ($commentaires as $key => $commentaire) {
            $commentaires[$key] = explode("\n\t", $commentaire);

            if (!strstr($commentaires[$key][0], '@description ')) {
                $commentaires[$key][0] = '@description ' . $commentaires[$key][0];
            }
            if (count($commentaires[$key]) < 2) {
                $commentaires[$key] = $commentaires[$key][0];
            }
            $commentaires[$key] = explode("\n", $commentaires[$key]);

            foreach ($commentaires[$key] as $key2 => $commentaire2) {
                $commentaires[$key][$key2] = str_replace(" * ", '', $commentaire2);
                $commentaires[$key][$key2] = str_replace('* ', '', $commentaire2);

                //enlèvement des espaces en surplus.
                preg_replace_callback('`[\ ]+([\@a-zA-Z0-9\ \$\?\-\_\/\:\.é\\\'àùè]+)`', function ($matches) use (&$commentaires, $key2, $key) {
                    $commentaires[$key][$key2] = $matches[1];
                }, $commentaires[$key][$key2]);
                if ($commentaire2 === ' *') {
                    unset($commentaires[$key][$key2]);
                }
            }
            $commentaires[$key][] = '@model ' . $class;
        }

        foreach ($commentaires as $key => $commentaire) {
            $commentaires[$key][0] = '@description ' . ucfirst($commentaire[0]);
        }

        foreach ($commentaires as $key => $commentaire) {
            foreach ($commentaire as $k => $v) {
                $tmp = [];
                preg_replace_callback('`(\@[&-zA-Z\_\-\/\:]+)\ ([^µ]+)`', function ($matches) use (&$tmp) {
                    $tmp = [$matches[1], $matches[2]];
                }, $v);
                $commentaires[$key][$k] = $tmp;
            }
        }

        foreach ($commentaires as $key => $commentaire) {
            foreach ($commentaire as $k => $v) {
                if (!empty($v)) {
                    $commentaires[$key][$v[0]] = $v[1];
                    if ($v[0] === '@param') {
                        if (strstr($commentaires[$key]['@param'], ' ')) {
                            $var = explode(' ', $commentaires[$key]['@param']);
                            if (isset($var[1])) {
                                $name = $var[1];
                                $value = $var[0];
                                if (gettype($commentaires[$key]['@param']) === 'array') {
                                    $commentaires[$key]['@param'][$name] = $value;
                                } else {
                                    $commentaires[$key]['@param'] = [$name => $value];
                                }
                            } else {
                                $value = $var[0];
                                if (gettype($commentaires[$key]['@param']) === 'array') {
                                    $commentaires[$key]['@param'][] = $value;
                                } else {
                                    $commentaires[$key]['@param'] = [$value];
                                }
                            }
                        }
                    }
                }
                unset($commentaires[$key][$k]);
            }
        }

        $this->commentaires[] = $commentaires;
    }

    public function __call($name, $arguments)
    {
        if (is_file('../../custom/annotation/' . $name . '.php')) {
            require_once '../../custom/annotation/' . $name . '.php';
            return new $name($this->commentaires);
        }
        return null;
    }

    public function to_html()
    {

    }

    public function reset()
    {
        $this->commentaires = [];
    }
}

var_dump((new PhpDocParser('../../custom/mvc/models/HelloWorld_model.php'))->route()->get());
var_dump((new PhpDocParser('../../custom/mvc/models/HelloWorld_model.php'))->httpVerb()->get());