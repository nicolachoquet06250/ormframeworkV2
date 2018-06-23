<?php
namespace ormframework\core\commands;

use ormframework\core\setup\utils;

class help extends command
{
    private $commands = [];

    public function __construct()
    {
        $this->commands = json_decode(file_get_contents('core/commands/enable_commands.json'), true);
    }

    public function exec() {
        foreach ($this->commands as $command => $details) {
            $string = 'php ormframework.php '.$command.' do ';
            if($command === 'help') {
                $string = 'php ormframework.php'
                    ."\n".'php ormframework.php -h'
                    ."\n".'php ormframework.php --help';
                echo $this->color_cli_text($string)."\n";
            }
            if(!empty($details)) {
                foreach ($details as $detail) {
                    $string = 'php ormframework.php '.$command.' do '.str_replace('_', ' ', $detail['method']);
                    for ($i = 0, $max = $detail['args']; $i < $max; $i++) {
                        if ($i === 0) {
                            $string .= ' -p';
                        }
                    }
                    if(isset($detail['keys'])) {
                        foreach ($detail['keys'] as $id => $key) {
                            $string .= " {$key}=";
                            $string .= '<value>';
                        }
                    }
                    $color = isset($detail['important']) ? self::RED_TEXT : self::WHITE_TEXT;
                    echo $this->color_cli_text($string, $color)."\n";
                }
            }
        }
    }
}