<?php

/**
 * Request short summary.
 *
 * Request description.
 *
 * @version 1.0
 * @author Buffreak
 */
namespace Lib\Api;
class Request
{
    /**
     * Request to your API or resource
     * @param string $url
     * @param string[]|bool $postFields
     * @param array $headers
     * @param bool|string $cookie if you want store cookie with CURLOPT_COOKIEJAR and CURLOPT_COOKIEFILE switch it to cookiename, default dir in /inc/cookies/{$cookiename}
     * @return array|string[]
     */
    public static function curl($url, $postFields = false, $headers = [], $cookie = false){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $postFields ? curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields) : '';
        $headers ? curl_setopt($ch, CURLOPT_HTTPHEADER, (array) $headers) : '';
        if($cookie):
            curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__.'/../../inc/cookies/'.$cookie);
            curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__.'/../../inc/cookies/'.$cookie);
        endif;
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $exec = curl_exec($ch);
        $headerBuffer = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        return ['header' => substr($exec, 0, $headerBuffer), 'body' => substr($exec, $headerBuffer)];
    }

    /**
     * Generate random string
     * @param int $length
     * @param string $type three parameter option: "string" for alphabets only, "integer" for number only, "special_characters" for special characters
     */
    public static function generateString($length = 8, $type = 'string'){
        $str = "";
        $containers = [
            'string' => 'abcdefghijklmnopqrstuvwxyz',
            'integer' => '0123456789',
            'special_characters' => '!@#$%^&*()_+-='
        ];
        for($i = 0; $i < $length; $i++) $str .= $containers[$type][rand(0, strlen($containers[$type]) - 1)];
        return $str;
    }

    public static function input(){
        return trim(fgets(STDIN));
    }
}