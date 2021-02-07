<?php

/**
 * FakeName short summary.
 *
 * FakeName description.
 *
 * @version 1.0
 * @author Buffreak
 */
namespace Lib\Api;

class FakeName
{
    const URL = [
        "home" => 'https://api.namefake.com/indonesian-indonesia'
    ];

    public static function info(){
        $request = json_decode(Request::curl(self::URL['home'])['body'], true);
        return $request;
    }
}