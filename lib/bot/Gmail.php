<?php

/**
 * Gmail short summary.
 *
 * Gmail description.
 *
 * @version 1.0
 * @author Buffreak
 */
namespace Lib\Bot;
use Lib\Api\Request;
use Lib\Api\FakeName;
use Lib\Api\ClientConfig;
use Lib\Api\DeathByCaptcha_Client;
use Lib\Api\WebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Cookie;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\WebDriverKeyboard;
class Gmail extends WebDriver
{
    protected $otp, $email, $password, $name, $maidenName;
    const DEFAULT_SLEEP = 2;

    const URL = [
        "signup_form" => "https://accounts.google.com/SignUp?service=mail&continue=https://mail.google.com/mail/?pc=topnav-about-n-en"
    ];

    public function __construct($incognitoMode = false, $headlessMode = false){
        parent::__construct($incognitoMode, $headlessMode);
    }

    protected function initFakeData(){
        $fakeinfo = FakeName::info();
        $this->email = $fakeinfo['username'].Request::generateString(3).Request::generateString(2, 'integer');
        $this->password = $fakeinfo['password'];
        $this->name = $fakeinfo['name'];
        $this->maidenName = $fakeinfo['maiden_name'];
    }

    public function init(){
        $this->loadChrome(); // $this->driver loaded from this method
        $this->initFakeData();
        $this->driver->get(self::URL['signup_form']);
        $this->driver->wait()->until(WebDriverExpectedCondition::titleIs('Buat Akun Google'));

        // $this->driver->findElement(WebDriverBy::cssSelector('span.VfPpkd-vQzf8d'))
        //         ->click();
        // sleep(self::DEFAULT_SLEEP);
        // $this->driver->getKeyboard()->pressKey(WebDriverKeys::ENTER);
        // $this->driver->wait()->until(WebDriverExpectedCondition::titleIs('Buat Akun Google'));

        $this->delayInput(function(){
            return $this->driver->findElements(WebDriverBy::tagName('input'))[0];
        }, preg_replace('/[,.]/', '', $this->name));

        $this->delayInput(function(){
            return $this->driver->findElements(WebDriverBy::tagName('input'))[1];
        }, preg_replace('/[,.]/','', $this->maidenName));

        $this->delayInput(function(){
            return $this->driver->findElements(WebDriverBy::tagName('input'))[2];
        }, $this->email);

        $this->delayInput(function(){
            return $this->driver->findElements(WebDriverBy::tagName('input'))[3];
        }, preg_replace('/\|/','', $this->password));

        $this->delayInput(function(){
            return $this->driver->findElements(WebDriverBy::tagName('input'))[4];
        }, preg_replace('/\|/','', $this->password));

        // $this->driver->findElement(WebDriverBy::cssSelector('input.VfPpkd-muHVFf-bMcfAe'))
        //     ->click(); // Show Password

        $this->driver->findElement(WebDriverBy::tagName('button'))
             ->click(); // First Next
        sleep(4);

        $checkIfOTP = $this->driver->findElements(WebDriverBy::tagName('h1'))[0]->getAttribute("innerHTML");
        if(stripos($checkIfOTP, "Verifikasi no. telp.") !== false){
            if($this->OTPVerification()){
                $this->setBio();
            }
        }
        if($this->config->ip_mode){
            echo "Sudah Menggunakan mode Pesawat? (Mengganti IP) : ";
            Request::input();
        }
        $this->deleteAllCookies();
        $this->closeBrowser();
    }
    protected function OTPVerification(){
        inputPhoneNumber: {

            $this->phoneNumberType("Masukkan Nomor HP : ");

            $this->delayInput(function(){
                return $this->driver->findElement(WebDriverBy::tagName('input'));
            }, $this->phoneNumber);

            $this->driver->findElement(WebDriverBy::tagName('button'))
                 ->click();
            sleep(4);
            $getErrorMessage = $this->driver->findElements(WebDriverBy::xpath("//div[@aria-live='assertive']"));
            if(count($getErrorMessage) > 0){
                $getErrorMessage = $getErrorMessage[0]->getAttribute("innerHTML");
                if(stripos($getErrorMessage, 'Nomor telepon ini tidak dapat digunakan untuk verifikasi.') !== false || stripos($getErrorMessage, 'Nomor ponsel ini sudah terlalu sering digunakan.') !== false || stripos($getErrorMessage, "Format nomor telepon ini tak dikenal. Coba cek lagi kode negara dan nomornya") !== false){
                    echo "Nomor => ".$this->phoneNumber." Sudah Tidak bisa digunakan atau sudah terlalu sering digunakan untuk verifikasi\n";
                    $this->driver->findElement(WebDriverBy::tagName('input'))->clear();
                    goto inputPhoneNumber;
                }
            }
        }
        while(1){
            $this->otpType("Masukkan OTP yang diterima ke ".$this->phoneNumber." : ");
            if($this->OTP){
                $this->delayInput(function(){
                    return $this->driver->findElement(WebDriverBy::tagName('input'));
                }, $this->OTP);
    
                sleep(1);
                $this->driver->findElements(WebDriverBy::tagName('button'))[1]->click();
                sleep(4);
    
                $getErrorMessage = $this->driver->findElements(WebDriverBy::xpath("//div[@aria-live='assertive']"));
                if(count($getErrorMessage) > 0){
                    $getErrorMessage = $getErrorMessage[0]->getAttribute("innerHTML");
                    if(stripos($getErrorMessage, 'Kode salah. Coba lagi.') === false) break;
                }else break;
            }else return false;
        }
        return true;
    }

    protected function setBio(){
        sleep(self::DEFAULT_SLEEP);

        $this->delayInput(function(){
            return $this->driver->findElements(WebDriverBy::tagName('input'))[2];
        }, rand(1, 29));

        $this->driver->findElements(WebDriverBy::tagName('select'))[0]->click();
        for($i = 0; $i < rand(1, 12); $i++){
            $this->driver->getKeyboard()->pressKey(WebDriverKeys::ARROW_DOWN);
            usleep(110000);
        }
        $this->driver->getKeyboard()->pressKey(WebDriverKeys::ENTER);
        sleep(1);

        $this->delayInput(function(){
            return $this->driver->findElements(WebDriverBy::tagName('input'))[3];
        }, rand(1990, 2001));

        $this->driver->findElements(WebDriverBy::tagName('select'))[1]->click();
        for($i = 0; $i < rand(1, 2); $i++){
            $this->driver->getKeyboard()->pressKey(WebDriverKeys::ARROW_DOWN);
            usleep(110000);
        }
        $this->driver->getKeyboard()->pressKey(WebDriverKeys::ENTER);
        $this->driver->findElements(WebDriverBy::tagName('button'))[0]->click();
        sleep(3);
        $this->driver->findElements(WebDriverBy::tagName('button'))[3]->click();
        sleep(3);
        $this->driver->executeScript('window.scrollTo(0,document.body.scrollHeight);');
        sleep(2);
        $this->driver->findElements(WebDriverBy::tagName('button'))[3]->click();
        fwrite(fopen(WebDriver::INC_PATH."/gmail.txt", 'a'), $this->email."@gmail.com|".$this->password."|".$this->phoneNumber."|".date("Y-m-d H:i:s").PHP_EOL);
        $this->driver->wait()->until(WebDriverExpectedCondition::titleContains('Kotak Masuk'));
    }
}