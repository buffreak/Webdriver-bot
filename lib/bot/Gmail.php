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
    protected $phoneNumber, $otp, $email, $password, $name, $maidenName;
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

    public function registerGmail(){
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
            return $this->driver->findElement(WebDriverBy::cssSelector('input#firstName.whsOnd.zHQkBf'));
        }, preg_replace('/[,.]/', '', $this->name));

        $this->delayInput(function(){
            return $this->driver->findElement(WebDriverBy::cssSelector('input#lastName.whsOnd.zHQkBf'));
        }, preg_replace('/[,.]/','', $this->maidenName));

        $this->delayInput(function(){
            return $this->driver->findElement(WebDriverBy::cssSelector('input#username.whsOnd.zHQkBf'));
        }, $this->email);

        $this->delayInput(function(){
            return $this->driver->findElement(WebDriverBy::name('Passwd'));
        }, preg_replace('/\|/','', $this->password));

        $this->delayInput(function(){
            return $this->driver->findElement(WebDriverBy::name('ConfirmPasswd'));
        }, preg_replace('/\|/','', $this->password));

        // $this->driver->findElement(WebDriverBy::cssSelector('input.VfPpkd-muHVFf-bMcfAe'))
        //     ->click(); // Show Password

        $this->driver->findElement(WebDriverBy::cssSelector('span.VfPpkd-vQzf8d'))
             ->click(); // First Next
        sleep(4);

        $checkIfOTP = $this->driver->findElements(WebDriverBy::cssSelector('h1#headingText.ahT6S span'))[0]->getAttribute("innerHTML");
        if(stripos($checkIfOTP, "Verifikasi no. telp.") !== false){
            if($this->OTPVerification()){
                $this->setBio();
            }
        }
        if($this->config->gmail->ip_mode){
            echo "Sudah Menggunakan mode Pesawat? (Mengganti IP) : ";
            Request::input();
        }
        $this->deleteAllCookies();
        $this->closeBrowser();
    }
    protected function OTPVerification(){
        inputPhoneNumber: {
            echo "Masukkan nomor HP : ";
            $this->phoneNumber = Request::input();

            $this->delayInput(function(){
                return $this->driver->findElement(WebDriverBy::cssSelector('input#phoneNumberId.whsOnd.zHQkBf'));
            }, $this->phoneNumber);

            $this->driver->findElement(WebDriverBy::cssSelector('div.VfPpkd-RLmnJb'))
                 ->click();
            sleep(4);
            $getErrorMessage = $this->driver->findElements(WebDriverBy::cssSelector('div.o6cuMc'));
            if(count($getErrorMessage) > 0){
                $getErrorMessage = $getErrorMessage[0]->getAttribute("innerHTML");
                if(stripos($getErrorMessage, 'Nomor telepon ini tidak dapat digunakan untuk verifikasi.') !== false || stripos($getErrorMessage, 'Nomor ponsel ini sudah terlalu sering digunakan.') !== false){
                    echo "Nomor => ".$this->phoneNumber." Sudah Tidak bisa digunakan atau sudah terlalu sering digunakan untuk verifikasi\n";
                    $this->driver->findElement(WebDriverBy::cssSelector('input#phoneNumberId.whsOnd.zHQkBf'))->clear();
                    goto inputPhoneNumber;
                }
            }
        }
        $getMessage = $this->driver->findElement(WebDriverBy::cssSelector('div.PrDSKc'))->getAttribute("innerHTML");
        if(stripos($getMessage, 'Demi keamanan, Google ingin memastikan ini memang Anda. Google akan mengirim SMS berisi kode verifikasi 6 digit.') !== false){
            while(1){
                echo "Masukkan OTP yang masuk ke nomor {$this->phoneNumber} : ";
                $this->otp = Request::input();

                $this->delayInput(function(){
                    return $this->driver->findElement(WebDriverBy::cssSelector('input#code.whsOnd.zHQkBf'));
                }, $this->otp);

                sleep(1);
                $this->driver->findElements(WebDriverBy::cssSelector('div.VfPpkd-RLmnJb'))[1]->click();
                sleep(4);

                $getErrorMessage = $this->driver->findElements(WebDriverBy::cssSelector('div.o6cuMc'));
                if(count($getErrorMessage) > 0){
                    $getErrorMessage = $getErrorMessage[0]->getAttribute("innerHTML");
                    if(stripos($getErrorMessage, 'Kode salah. Coba lagi.') === false) break;
                }else{
                    break;
                }
            }
        }
        return true;
    }

    protected function setBio(){
        sleep(self::DEFAULT_SLEEP);

        $this->delayInput(function(){
            return $this->driver->findElement(WebDriverBy::cssSelector('input#day.whsOnd.zHQkBf'));
        }, rand(1, 29));

        $this->driver->findElement(WebDriverBy::cssSelector('select#month.UDCCJb'))->click();
        for($i = 0; $i < rand(1, 12); $i++){
            $this->driver->getKeyboard()->pressKey(WebDriverKeys::ARROW_DOWN);
            usleep(110000);
        }
        $this->driver->getKeyboard()->pressKey(WebDriverKeys::ENTER);
        sleep(1);

        $this->delayInput(function(){
            return $this->driver->findElement(WebDriverBy::cssSelector('input#year.whsOnd.zHQkBf'));
        }, rand(1990, 2001));

        $this->driver->findElement(WebDriverBy::cssSelector('select#gender.UDCCJb'))->click();
        for($i = 0; $i < rand(1, 2); $i++){
            $this->driver->getKeyboard()->pressKey(WebDriverKeys::ARROW_DOWN);
            usleep(110000);
        }
        $this->driver->getKeyboard()->pressKey(WebDriverKeys::ENTER);
        $this->driver->findElements(WebDriverBy::cssSelector('div.VfPpkd-RLmnJb'))[0]->click();
        sleep(3);
        $this->driver->findElements(WebDriverBy::cssSelector('div.VfPpkd-RLmnJb'))[1]->click();
        sleep(3);
        $this->driver->executeScript('window.scrollTo(0,document.body.scrollHeight);');
        sleep(2);
        $this->driver->findElements(WebDriverBy::cssSelector('.RveJvd.snByac'))[0]->click();
        fwrite(fopen(WebDriver::INC_PATH."/gmail.txt", 'a'), $this->email."@gmail.com|".$this->password."|".$this->phoneNumber."|".date("Y-m-d H:i:s").PHP_EOL);
        $this->driver->wait()->until(WebDriverExpectedCondition::titleContains('Kotak Masuk'));
    }
}