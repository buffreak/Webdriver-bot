# Webdriver Bot

Webdriver bot menggunakan selenium dan bahasa pemrograman PHP.

## Instalasi

* Pertama silahkan clone repo menggunakan git clone dengan command.

```
git clone https://github.com/buffreak/Webdriver-bot.git
```
* Pastikan sudah menginstall [JRE](https://www.oracle.com/java/technologies/javase/jdk11-archive-downloads.html) (Java Runtime Enviroment) dan path nya sudah di setting.
* Pastikan sudah menginstall [PHP](https://www.php.net/) <= 7.1
* Pastikan Chrome / Chromium Versi 88, jika versi diatas atau dibawah 88 silahkan mengganti **chromedriver.exe** yang ada di root direktori sesuai dengan versi chrome / chromium yang kalian punya. Chromedriver dapat didownload [disini](https://chromedriver.chromium.org/).

## Penggunaan

* Buka __clientConfig.json__ di root direktori lalu setting path chrome / chromium yang terinstall di OS kalian.
* Buka CMD / Powershell lalu masukkan perintah.
* Setting config jika ingin menggunakan api dari [SMS-ACTIVE.RU](https://sms-active.ru) di clientConfig.json
```
java -jar selenium.jar
```
* Buka CMD / Poweshell kedua lalu jalankan perintah.
```
php run.php
```
## Bot yang tersedia
1. Gmail Auto Regist [SELENIUM]
2. Twitter Auto Regist [SELENIUM]

## Pemberitahuan
Pastikan sudah menyidiakan nomor HP untuk OTP jika menggunakan Gmail Bot, dan saya tidak bertanggung jawab jika akun Google dinonaktifkan karena menggunakan bot ini.
Bot ini tidak bisa berjalan di __Termux__.

## License
[MIT](https://choosealicense.com/licenses/mit/)