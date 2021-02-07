<?php
require_once __DIR__.'/autoload.php';
use Lib\Bot\Gmail;
echo '
█▄▀ █▀▀ █▀█ ░░█ ▄▀█ █▀█ █▀█ █▀▄ █ ░ █▀▀ █▀█ █▀▄▀█
█░█ ██▄ █▀▄ █▄█ █▀█ █▀▄ █▄█ █▄▀ █ ▄ █▄▄ █▄█ █░▀░█
';
echo "Beta Version V1.0\n\n";

try{
    $bot = new Gmail();
    for(;;){
        $bot->registerGmail();
    }
}catch(Exception $e){
    echo $e->getMessage()." in line: ".$e->getLine()."\n";
}