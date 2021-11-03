<?php
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

class Twitter extends WebDriver{

    protected $phoneNumber, $otp, $email, $password, $name, $maidenName, $username;

    const URL = [
        'signup' => 'https://twitter.com/i/flow/signup'
    ];

    public function __construct($incognitoMode = false, $headlessMode = false)
    {
        parent::__construct($incognitoMode, $headlessMode);
    }

    public function init(){
        $this->loadChrome(); // $this->driver loaded from this method
        $this->setBio();
        if($this->getOTP()){
            $this->goToProfile();
        }
        if($this->config->ip_mode){
            echo "Sudah Menggunakan mode Pesawat? (Mengganti IP) : ";
            Request::input();
        }
        $this->deleteAllCookies();
        $this->closeBrowser();
    }

    protected function setBio(){
        $this->initFakeData();
        $this->driver->get(self::URL['signup']);
        $this->driver->wait()->until(WebDriverExpectedCondition::titleContains('Sign up for Twitter'));
        //Name Input
        $this->delayInput(function(){
            return $this->driver->findElement(WebDriverBy::name("name"));
        }, preg_replace('/[,.]/','', $this->name));
        // Phone_number Input
        $this->phoneNumberType("Masukkan Phone Number : ", "tw");
        $this->delayInput(function(){
            return $this->driver->findElement(WebDriverBy::name("phone_number"));
        }, preg_replace('/[,.]/','', $this->phoneNumber));
        //Select all List births
        $getAllBirth =  $this->driver->findElements(WebDriverBy::tagName('select'));
        // select months by element
        $getAllBirth[0]->click();
        for($i = 0; $i < rand(1, 12); $i++){
            $this->driver->getKeyboard()->pressKey(WebDriverKeys::ARROW_DOWN);
            usleep(110000);
        }
        // select Days by element
        $getAllBirth[1]->click();
        for($i = 0; $i < rand(1, 31); $i++){
            $this->driver->getKeyboard()->pressKey(WebDriverKeys::ARROW_DOWN);
            usleep(110000);
        }
        // select years by element
        $getAllBirth[2]->click();
        for($i = 0; $i < rand(27, 30); $i++){
            $this->driver->getKeyboard()->pressKey(WebDriverKeys::ARROW_DOWN);
            usleep(110000);
        }
        // press enter for exit last selected in list
        $this->driver->getKeyboard()->pressKey(WebDriverKeys::ENTER);
        
        sleep(1);
        for($i = 0; $i < 3; $i++){
            $countInteresting = $this->driver->findElements(WebDriverBy::xpath("//div[@role='button']"));
            $countInteresting[count($countInteresting) - 1]->click();
            sleep(1);
        }
        $this->driver->findElements(WebDriverBy::xpath("//div[@data-testid='confirmationSheetConfirm']"))[0]->click();
        return $this;
    }

    protected function getOTP(){
       $this->otpType("Masukkan OTP yang diterima ke ".$this->phoneNumber." : ");
        if($this->OTP){
            $this->delayInput(function(){
                return $this->driver->findElement(WebDriverBy::name("verfication_code"));
            }, preg_replace('/[,.]/','', $this->OTP));
            // Submit OTP Button
            sleep(1);
            $this->driver->findElements(WebDriverBy::xpath("//div[@role='button']"))[1]->click();
            // Isi Password
            sleep(2);
            $this->delayInput(function(){
                return $this->driver->findElement(WebDriverBy::name("password"));
            }, preg_replace('/[,.]/','', $this->password));
    
            sleep(2);
            for($i = 0; $i < 8; $i++){
                $countInteresting = $this->driver->findElements(WebDriverBy::xpath("//div[@role='button']"));
                $countInteresting[count($countInteresting) - 1]->click();
                sleep(1);
            }
            return true;
        }
        return false;
    }

    protected function goToProfile(){
        preg_match("/[^\/]+$/", $this->driver->findElement(WebDriverBy::xpath("//a[@aria-label='Profile']"))->getAttribute("href"), $matches);
        $this->username = $matches[0];
        fwrite(fopen(WebDriver::INC_PATH."/twitter.txt", 'a'), $this->username."|".$this->password."|".$this->phoneNumber."|".date("Y-m-d H:i:s").PHP_EOL);
        return $this;
    }

}
?>