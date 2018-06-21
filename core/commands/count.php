<?php

namespace ormframework\core\commands;


class count extends command
{
    public function __construct(array $args = [])
    {
        $this->argv = $args;
    }

    private function parcour_dir(&$nbr, $path = './') {
        $dir = opendir($path);
        while (($file = readdir($dir)) !== false) {
            if(is_file($path.$file) && explode('.', $file)[1] === 'php') {
                $content = file_get_contents($path.$file);
                $content = explode("\n", $content);
                $nbr += count($content);
            }
            elseif(is_dir($path.$file) && substr($file, 0, 1) !== '.') {
                $this->parcour_dir($nbr, $path.$file.'/');
            }
        }
    }

    public function nbr_php_lignes_into_project() {
        $nbr = 0;
        $this->parcour_dir($nbr);
        var_dump($nbr);
    }
}