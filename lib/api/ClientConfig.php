<?php

/**
 * ClientConfig short summary.
 *
 * ClientConfig description.
 *
 * @version 1.0
 * @author Buffreak
 */
namespace Lib\Api;
trait ClientConfig
{
    protected $config;

    public function setConfigFile($filename = "clientConfig.json", ?bool $toArray = false){
        date_default_timezone_set("Asia/Jakarta");
        $this->config = json_decode(file_get_contents(__DIR__.'/../../'.$filename), $toArray);
    }
}