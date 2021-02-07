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
class ClientConfig
{
    protected $config;
    const INC_PATH = __DIR__.'/../../inc';

    public function __construct($filename = "clientConfig.json", ?bool $toArray = null){
        date_default_timezone_set("Asia/Jakarta");
        $this->config = json_decode(file_get_contents(__DIR__.'/../../'.$filename), $toArray);
    }
}