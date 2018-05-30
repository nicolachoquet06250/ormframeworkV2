<?php

class help extends command
{
    private $commands = [];

    public function __construct()
    {
        $this->commands = [
            'help' => [
                'method' => 'exec',
                'args' => 0
            ],
            'hello' => [
                [
                    'method' => 'say',
                    'args' => 2
                ],
                [
                    'method' => 'world',
                    'args' => 1
                ]
            ]
        ];
    }

    public function exec() {
        foreach ($this->commands as $command => $details) {
            $string = 'php ormframework.php '.$command.' do ';
            if($command === 'help') {
                $string = 'php ormframework.php'
                    ."\n".'php ormframework.php -h'
                    ."\n".'php ormframework.php --help';
            }

            if(isset($details[0])) {
                foreach ($details as $detail) {
                    $string = 'php ormframework.php '.$command.' do '.$detail['method'];
                    for ($i = 0, $max = $detail['args']; $i < $max; $i++) {
                        if ($i === 0) {
                            $string .= ' -p';
                        }
                        $string .= ' < arg ' . $i . ' >';
                    }
                    echo $string."\n";
                }
            }
            else {
                $string .= $details['method'];
                for ($i = 0, $max = $details['args']; $i < $max; $i++) {
                    if ($i === 0) {
                        $string .= ' -p';
                    }
                    $string .= ' < arg ' . $i . ' >';
                }
                echo $string."\n";
            }
        }
    }
}