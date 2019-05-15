# Kufar API
The Unofficial www.kufar.by API

## Requirements
1. PHP 7.1.3 or higher

## Basic usage
```php
<?php

require_once 'vendor/autoload.php';

use API\Kufar\Kufar;
use API\Kufar\Adverts\Flat;

set_time_limit(0);
header('Content-Type: text/plain');

/////////// CONFIG ///////////
$debug     = true;
$debugFile = null;
$user      = 'user_name';
$password  = 'user_password';
//////////////////////////////

///////////////////////// IMAGES UPLOAD /////////////////////////
$images[] = $kufar->uploadImage('images/image1.jpg')['img_link'];
$images[] = $kufar->uploadImage('images/image2.jpg')['img_link'];
$images[] = $kufar->uploadImage('images/image3.jpg')['img_link'];
$images[] = $kufar->uploadImage('images/image4.jpg')['img_link'];
$images[] = $kufar->uploadImage('images/image5.jpg')['img_link'];
/////////////////////////////////////////////////////////////////

/////////////////////////// ADVERT CREATION ///////////////////////////
$flatAdvert = new Flat(
    'Однокомнатная квартира, МОПРа ул. - 390267',
    1,
    '1- комнатная квартира, г. Брест, МОПРа ул., 1978 г.п. Лот 390267',
    29900,
    $flatAdvert->findCurrencyTypeByName('$'),
    $region = $kufar->findRegionByName('Брест'),
    $kufar->findAreaByName($region, 'Брест'),
    'МОПРа ул.',
    4,
    41.7,
    17.1,
    9.8,
    $flatAdvert->findHouseTypeByName('панельные'),
    $flatAdvert->findBathroomTypeByName('раздельный'),
    $flatAdvert->findBalconyTypeByName('нет'),
    1978,
    $images,
    ['MTS: (+375 33) 344-44-67', 'Velcom: (+375 44) 581-64-07'],
    'Алла Николаевна',
    'https://bugrealt.by/kvartiry-komnaty/flats/390267'
);
///////////////////////////////////////////////////////////////////////

$kufar = new Kufar($debug, $debugFIle);

try {

    $kufar->login($user, $password);

    $flatAdvert->setAccountInfo($kufar->getAccountInfo());
    $flatAdvert->->setAddressInfo($kufar->getAddressInfo('Брест', 'Брест', 'МОПРа ул.'));

    $result = $kufar->postAdvert($flatAdvert);

    if (isset($result['ad_id'])) {
        // Success
    } else {
        // Failed
    }

} catch (Throwable $e) {

    echo 'Some error occurred: '.$e->getMessage();
    exit(-1);

}
```
