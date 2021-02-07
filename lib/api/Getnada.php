<?php

/**
 * Getnada short summary.
 *
 * Getnada description.
 *
 * @version 1.0
 * @author Buffreak
 */
namespace Lib\Api;
class Getnada
{
    protected $email, $verificationCode;

    const URL = [
        'check_inbox' => 'https://getnada.com/api/v1/inboxes/',
        'get_message' => 'https://getnada.com/api/v1/messages/html/',
        'refresh_mailbox'=> 'https://getnada.com/api/v1/u/', // Next Domain email name and timestamp
    ];

    /**
     * Generate New Email
     * @param bool $random
     * @param string $email if random set to false you must fill $email parameter
     * @return Getnada
     */
    public function getEmail($random = true, $email = ""){

        $random ? $this->email = Request::generateString()."@getnada.com " : $email;
        return $this;
    }

    /**
     * Get Email By Regex
     * @param string $pattern
     * @param int $sleep How Many Time Sleep until email received
     * @return Getnada
     */
    public function getMailbox($pattern, int $sleep = 5){
        sleep($sleep);
        Request::curl(self::URL['refresh_mailbox'].$this->email."/".time())['body'];
        $checkMailBox = json_decode(Request::curl(self::URL['check_inbox'].$this->email)['body'], true);
        $readInbox = Request::curl(self::URL['get_message'].$checkMailBox['msgs'][0]['uid'])['body'];
        preg_match($pattern, $readInbox, $verificationCode);
        $this->verificationCode = trim($verificationCode[1]);
        return $this;
    }
}