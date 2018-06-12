<?php
namespace ormframework\core\commands;

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
                echo $string . "\n";
            }
            if(!empty($details)) {
                foreach ($details as $detail) {
                    $string = 'php ormframework.php '.$command.' do '.$detail['method'];
                    for ($i = 0, $max = $detail['args']; $i < $max; $i++) {
                        if ($i === 0) {
                            $string .= ' -p';
                        }
                        $string .= ' < arg ' . $i . ' >';
                    }
                    echo $string . "\n";
                }
            }
        }
    }
}