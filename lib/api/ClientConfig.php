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
use Lib\Api\SMSActivate;
trait ClientConfig
{
    protected $config, $sms, $numberResponse, $phoneNumber, $OTP;

    public function setConfigFile($filename = "clientConfig.json", ?bool $toArray = false){
        date_default_timezone_set("Asia/Jakarta");
        $this->config = json_decode(file_get_contents(__DIR__.'/../../'.$filename), $toArray);
    }

    protected function smsConfigRU(){
        $this->sms = new SMSActivate($this->config->sms_active_ru->apikey);
        echo "Saldo yang tersisa ".$this->sms->getBalance()." RUB (Russian Currency)\n";
        $this->numberResponse = $this->sms->getNumber($this->config->sms_active_ru->service, $this->config->sms_active_ru->country, 0);
        $this->phoneNumber = preg_replace('/.{2}(.+)$/', '0$1', $this->numberResponse["number"]);
        echo "Nomor Sekarang => ".$this->phoneNumber."\n";
        return $this;
    }

    protected function getMessageSmsRU(){
        $this->OTP = null;
        $status = $this->sms->setStatus($this->numberResponse['id'], 1);
        echo "Menunggu OTP dari nomor : ".$this->phoneNumber."\n";
        for($i = 1; $i <= 35; $i++){
            try{
                $status = $this->sms->getStatus($this->numberResponse['id']);
                if ($status['status'] === 'STATUS_OK' && $status['code']){
                    echo "Kode Nya : " .$status['code']."\n";
                    $this->OTP = $status['code'];
                    break;
                }
            }catch(\Exception $e){
                // Do something with this error in future...
            }
           
            sleep(1);
        }
        $this->OTP ? $this->sms->setStatus($this->numberResponse['id'], 6) : $this->sms->setStatus($this->numberResponse['id'], 8);
        return $this;
    }

    protected function prompInput(?string $message = null){
        echo $message;
        return trim(fgets(STDIN));
    }

    public function phoneNumberType(?string $message = null){
       $this->config->sms_active_ru->used ? $this->smsConfigRU() : $this->phoneNumber = $this->prompInput($message);
    }

    public function otpType(?string $message = null){
        $this->config->sms_active_ru->used ? $this->getMessageSmsRU() : $this->OTP = $this->prompInput($message);
    }
}