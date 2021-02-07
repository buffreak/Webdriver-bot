<?php
require_once __DIR__.'/vendor/autoload.php'; // load file from autoload composer 
$iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(__DIR__.'/lib/'),
            RecursiveIteratorIterator::SELF_FIRST);

foreach($iterator as $file) {
    if($file->isFile()) {
        require_once $file->getRealPath();
    }
}