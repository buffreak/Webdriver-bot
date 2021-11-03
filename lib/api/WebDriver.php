<?php

/**
 * WebDriver short summary.
 *
 * WebDriver description.
 *
 * @version 1.0
 * @author Buffreak
 */
namespace Lib\Api;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Cookie;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\WebDriverKeyboard;
use Lib\Api\Request;
use Lib\Api\FakeName;
use Lib\Api\ClientConfig;
use Lib\Api\DeathByCaptcha_Client;

class WebDriver implements Definition
{
    use ClientConfig;
    protected $incognitoMode, $chromeOptions, $capabilities, $headlessMode, $driver;

    public function __construct($incognitoMode = false, $headlessMode = false){
        $this->incognitoMode = ($incognitoMode) ? '--incognito' : '';
        $this->headlessMode = ($headlessMode) ? '--headless' : '';
        $this->setConfigFile();
    }

    public function loadChrome(){

        $this->chromeOptions = new ChromeOptions();
        $this->incognitoMode ? $this->chromeOptions->addArguments([$this->incognitoMode]) : '';
        $this->headlessMode ? $this->chromeOptions->addArguments([$this->headlessMode]) : '';
        $this->chromeOptions->addArguments(['--user-agent=Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:91.0) Gecko/20100101 Firefox/91.0']);
        $this->chromeOptions->addArguments(['--start-maximized']);
        $this->chromeOptions->addArguments(['window-size=1400,900']);
        $this->chromeOptions->setBinary($this->config->webdriver->chrome_path);
        $this->chromeOptions->setExperimentalOption("excludeSwitches", ["enable-automation"]);
        $this->capabilities = DesiredCapabilities::chrome();
        $this->capabilities->setCapability(ChromeOptions::CAPABILITY, $this->chromeOptions);


        $this->driver = RemoteWebDriver::create($this->config->webdriver->host, $this->capabilities);
        return $this;

    }

    protected function deleteAllCookies(){
        $this->driver->manage()->deleteAllCookies();
        return $this;
    }

    protected function delayInput(\Closure $selector, string $str, bool $randomSleep = true){
        for($i = 0; $i < strlen($str); $i++){
            $selector()->sendKeys($str[$i]);
            usleep($randomSleep ? rand(110000, 117876) : 0);
        }
    }

    protected function closeBrowser(){
        $this->driver->close();
        return $this;
    }
}