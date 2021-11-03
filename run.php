<?php
require_once __DIR__.'/autoload.php';
use Lib\Bot\Gmail;
use Lib\Bot\GmLikes;
use Lib\Bot\Twitter;

echo '
█▄▀ █▀▀ █▀█ ░░█ ▄▀█ █▀█ █▀█ █▀▄ █ ░ █▀▀ █▀█ █▀▄▀█
█░█ ██▄ █▀▄ █▄█ █▀█ █▀▄ █▄█ █▄▀ █ ▄ █▄▄ █▄█ █░▀░█
';
echo "Beta Version V1.0\n\n";
echo "
Masukkan Pilihan
1. GMAIL
2. Twitter
3. GMLikes
Masukkan Pilihan : ";
$pilihan = (int) trim(fgets(STDIN));
$bot = null;
echo "\n";
try{
   switch($pilihan){
       case 1:
            $bot = new Gmail();
        break;
        case 2:
            $bot = new Twitter();
        break;
        default:
            throw new Exception("Salah Input");
        break;

   }
    for(;;){
        $bot->init();
    }
}catch(Exception $e){
    echo $e->getMessage()." in line: ".$e->getLine()."\n";
}