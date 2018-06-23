<?php

namespace ormframework\core\commands;

use Exception;

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

    public function nbr_php_lines_into_project() {
        $part = $this->get_from_name('part') ? './'.$this->get_from_name('part').'/' : './';
        $message = $this->get_from_name('part') ? "La partie {$this->get_from_name('part')} de votre projet compte {nbr} lignes" : "Votre projet compte {nbr} au total";
        $nbr = 0;
        $this->parcour_dir($nbr, $part);
        throw new Exception(str_replace('{nbr}', $nbr, $message));
    }
}