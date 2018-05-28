<?php

$dir = opendir('./custom/entities');
while (($file = readdir($dir)) !== false) {
    if($file !== '.' && $file !== '..') {
        require_once './custom/entities/'.$file;
    }
}