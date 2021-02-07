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

class WebDriver extends ClientConfig
{
    protected $incognitoMode, $chromeOptions, $capabilities, $headlessMode, $driver;

    public function __construct($incognitoMode = false, $headlessMode = false){
        $this->incognitoMode = ($incognitoMode) ? '--incognito' : '';
        $this->headlessMode = ($headlessMode) ? '--headless' : '';
        parent::__construct();
    }

    public function loadChrome(){

        $this->chromeOptions = new ChromeOptions();
        $this->incognitoMode ? $this->chromeOptions->addArguments([$this->incognitoMode]) : '';
        $this->headlessMode ? $this->chromeOptions->addArguments([$this->headlessMode]) : '';
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

    protected function delayInput(\Closure $selector, string $str){
        for($i = 0; $i < strlen($str); $i++){
            $selector()->sendKeys($str[$i]);
            usleep(rand(110000, 117876));
        }
    }

    protected function closeBrowser(){
        $this->driver->close();
        return $this;
    }
}