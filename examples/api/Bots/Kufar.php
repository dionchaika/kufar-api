<?php

namespace API\Bots;

use PDO;
use Throwable;
use API\Logger;
use RuntimeException;
use API\BotInterface;
use API\Kufar\Adverts\Flat;
use API\Kufar\Adverts\House;
use API\Kufar\Adverts\Office;
use API\Kufar\Kufar as KufarAPI;
use Dionchaika\Database\Query\Query;
use Dionchaika\Database\PDOConnection;

class Kufar implements BotInterface
{
    const IMAGES = __DIR__.'/../images';
    const DB_CONFIG = __DIR__.'/../config/db.php';
    const ACCOUNTS_CONFIG = __DIR__.'/../config/accounts.php';

    /**
     * @var \API\Kufar\Kufar
     */
    protected $kufar;

    /**
     * @var \Dionchaika\Database\Query\Query
     */
    protected $query;

    /**
     * @param bool        $debug
     * @param string|null $debugFile
     */
    public function __construct(bool $debug = false, ?string $debugFile = null)
    {
        $this->kufar = new KufarAPI($debug, $debugFile);

        $config = require self::DB_CONFIG;

        $dsn = 'mysql:'
            .'host='.$config['host']
            .';dbname='.$config['name']
            .';charset='.$config['charset'];

        try {
            $this->query = new Query(
                new PDOConnection(
                    new PDO(
                        $dsn,
                        $config['user'],
                        $config['password']
                    )
                )
            );
        } catch (Throwable $e) {
            Logger::log(Logger::LEVEL_FAILED, 'bugrealt', 'kufar', 'NaN', $e->getMessage());
            Logger::log(Logger::LEVEL_FAILED, 'alfa_active', 'kufar', 'NaN', $e->getMessage());
        }
    }

    /**
     * @param string $lot
     * @param string $company
     * @param string $account
     * @return void
     * @throws \RuntimeException
     */
    public function upload(string $lot, string $company = 'bugrealt', string $account = 'brest'): void
    {
        $config = require self::ACCOUNTS_CONFIG;

        if ('bugrealt' === $company) {
            if ('brest' === $account) {
                $user = $config['kufar']['bugrealt']['brest']['user'];
                $password = $config['kufar']['bugrealt']['brest']['password'];
            } else if ('pinsk' === $account) {
                $user = $config['kufar']['bugrealt']['pinsk']['user'];
                $password = $config['kufar']['bugrealt']['pinsk']['password'];
            } else if ('minsk' === $account) {
                $user = $config['kufar']['bugrealt']['minsk']['user'];
                $password = $config['kufar']['bugrealt']['minsk']['password'];
            } else {
                $user = $config['kufar']['bugrealt']['brest']['user'];
                $password = $config['kufar']['bugrealt']['brest']['password'];
            }
        } else {
            $user = $config['kufar']['alfa_active']['user'];
            $password = $config['kufar']['alfa_active']['password'];
        }

        try {
            $this->kufar->login($user, $password);
        } catch (Throwable $e) {
            Logger::log(Logger::LEVEL_FAILED, $company, 'kufar', $lot, $e->getMessage());
        }

        try {
            $object = $this->query
                ->select('*')
                ->from('objects')
                ->where('lot', '?')
                ->andWhere('disabled', 0)
                ->andWhere('xml_company', '?')
                ->setParameter($lot)
                ->setParameter($company)
                ->first();
        } catch (Throwable $e) {
            Logger::log(Logger::LEVEL_FAILED, $company, 'kufar', $lot, $e->getMessage());
        }

        if (empty($object)) {
            Logger::log(Logger::LEVEL_FAILED, $company, 'kufar', $lot, 'Object is not found!');
            return;
        }

        $region = KufarAPI::findRegionByName(!empty($object['region']) ? trim($object['region']) : 'Брестская');
        $area = KufarAPI::findAreaByName($region, !empty($object['city']) ? trim($object['city']) : 'Брест');

        $address = !empty($object['street']) ? trim($object['street']) : 'Советская улица';

        if ('flat' === $object['category']) {
            if ('bugrealt' === $company) {
                $importLink = 'https://bugrealt.by/kvartiry-komnaty/flats/'.$lot;
                $images = $this->getBugrealtImages($lot, $importLink);
            } else {
                $importLink = 'http://alfa-active.by/flats/'.$lot;
                $images = $this->getAlfaActiveImages($lot, $importLink);
            }

            $imgs = [];
            foreach ($images as $image) {
                try {
                    $imgs[] = $this->kufar->uploadImage($image)['img_link'];
                } catch (Throwable $e) {}

                unlink($image);
            }

            $advert = $this->getFlatAdvert($company, $lot, $object, $region, $area, $address, $imgs, $importLink);
        } else if ('house' === $object['category']) {
            if ('bugrealt' === $company) {
                $importLink = 'https://bugrealt.by/doma-dachi-uchastki/'.$lot;
                $images = $this->getBugrealtImages($lot, $importLink);
            } else {
                $importLink = 'http://alfa-active.by/houses/'.$lot;
                $images = $this->getAlfaActiveImages($lot, $importLink);
            }

            $imgs = [];
            foreach ($images as $image) {
                try {
                    $imgs[] = $this->kufar->uploadImage($image)['img_link'];
                } catch (Throwable $e) {
                    Logger::log(Logger::LEVEL_FAILED, $company, 'kufar', $lot, $e->getMessage());
                }

                unlink($image);
            }

            $advert = $this->getHouseAdvert($company, $lot, $object, $region, $area, $address, $imgs, $importLink);
        } else if ('office' === $object['category']) {
            if ('bugrealt' === $company) {
                $importLink = 'https://bugrealt.by/kommercheskaya-nedvizhimost/'.$lot;
                $images = $this->getBugrealtImages($lot, $importLink);
            } else {
                $importLink = 'http://alfa-active.by/offices/'.$lot;
                $images = $this->getAlfaActiveImages($lot, $importLink);
            }

            $imgs = [];
            foreach ($images as $image) {
                try {
                    $imgs[] = $this->kufar->uploadImage($image)['img_link'];
                } catch (Throwable $e) {
                    Logger::log(Logger::LEVEL_FAILED, $company, 'kufar', $lot, $e->getMessage());
                }

                unlink($image);
            }

            $advert = $this->getOfficeAdvert($company, $lot, $object, $region, $area, $address, $imgs, $importLink);
        } else {
            if ('bugrealt' === $company) {
                $importLink = 'https://bugrealt.by/kvartiry-komnaty/flats/'.$lot;
                $images = $this->getBugrealtImages($lot, $importLink);
            } else {
                $importLink = 'http://alfa-active.by/flats/'.$lot;
                $images = $this->getAlfaActiveImages($lot, $importLink);
            }

            $imgs = [];
            foreach ($images as $image) {
                try {
                    $imgs[] = $this->kufar->uploadImage($image)['img_link'];
                } catch (Throwable $e) {
                    Logger::log(Logger::LEVEL_FAILED, $company, 'kufar', $lot, $e->getMessage());
                }

                unlink($image);
            }

            $advert = $this->getFlatAdvert($company, $lot, $object, $region, $area, $address, $imgs, $importLink);
        }

        try {
            $advert->setAccountInfo($this->kufar->getAccountInfo());
        } catch (Throwable $e) {
            Logger::log(Logger::LEVEL_FAILED, $company, 'kufar', $lot, $e->getMessage());
        }

        try {
            $advert->setAddressInfo($this->kufar->getAddressInfo($region, $area, $address));
        } catch (Throwable $e) {
            Logger::log(Logger::LEVEL_FAILED, $company, 'kufar', $lot, $e->getMessage());
        }

        try {
            $result = $this->kufar->postAdvert($advert);
        } catch (Throwable $e) {
            Logger::log(Logger::LEVEL_FAILED, $company, 'kufar', $lot, $e->getMessage());
        }

        if (isset($result['ad_id'])) {
            Logger::log(Logger::LEVEL_SUCCESS, $company, 'kufar', $lot, json_encode(
                $result, JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            ));
        } else {
            Logger::log(Logger::LEVEL_FAILED, $company, 'kufar', $lot, json_encode(
                $result, JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            ));

            throw new RuntimeException('Uploading error!');
        }
    }

    /**
     * @param string $lot
     * @param string $uri
     * @return string[]
     */
    protected function getBugrealtImages(string $lot, string $uri): array
    {
        $page = @file_get_contents($uri.'?format=xml');
        if (false === $page) {
            return [];
        }

        if (false === preg_match_all('/\<image id\=\"\d+\"\>(.+)\<\/image\>/', $page, $matches)) {
            return [];
        }

        $images = [];
        $imageNumber = 1;

        foreach ($matches[1] as $match) {
            if (16 === $imageNumber) {
                break;
            }

            $imageContents = @file_get_contents($match);
            if (false === $imageContents) {
                continue;
            }

            $imageFilename = self::IMAGES.'/bugrealt/'.$lot.'_'.md5(microtime()).'_'.$imageNumber.'.jpg';
            if (false === file_put_contents($imageFilename, $imageContents)) {
                continue;
            }

            $images[] = $imageFilename;
            ++$imageNumber;
        }

        return $images;
    }

    /**
     * @param string $lot
     * @param string $uri
     * @return string[]
     */
    protected function getAlfaActiveImages(string $lot, string $uri): array
    {
        $page = @file_get_contents($uri);
        if (false === $page) {
            return [];
        }

        if (false === preg_match_all('/src\=\"(http\:\/\/alfa\-active\.by\/img_products\/thumb\_.+)\" alt\=/', $page, $matches)) {
            return [];
        }

        $images = [];
        $imageNumber = 1;

        foreach ($matches[1] as $match) {
            if (16 === $imageNumber) {
                break;
            }

            $imageContents = @file_get_contents(str_replace('thumb_', '', $match));
            if (false === $imageContents) {
                continue;
            }

            $imageFilename = self::IMAGES.'/alfa_active/'.$lot.'_'.md5(microtime()).'_'.$imageNumber.'.jpg';
            if (false === file_put_contents($imageFilename, $imageContents)) {
                continue;
            }

            $images[] = $imageFilename;
            ++$imageNumber;
        }

        return $images;
    }

    /**
     * @param string   $company
     * @param string   $lot
     * @param mixed[]  $object
     * @param int      $region
     * @param int      $area
     * @param string   $address
     * @param string[] $images
     * @param string   $importLink
     * @return \API\Kufar\Adverts\Flat
     */
    protected function getFlatAdvert(
        string $company,
        string $lot,
        array $object,
        int $region,
        int $area,
        string $address,
        array $images,
        string $importLink
    ): Flat {
        $currency = Flat::findCurrencyTypeByName('$');

        $houseType = Flat::findHouseTypeByName(!empty($object['wall_material']) ? trim($object['wall_material']) : 'Панельный');
        $bathroom = Flat::findBathroomTypeByName(!empty($object['bathroom']) ? trim($object['bathroom']) : 'Раздельный');
        $balcony = Flat::findBalconyTypeByName(!empty($object['balcony']) ? trim($object['balcony']) : 'Есть');

        try {
            return new Flat(
                !empty($object['title']) ? trim($object['title']) : '',
                !empty($object['rooms']) ? (int)$object['rooms'] : 1,
                !empty($object['description']) ? trim($object['description']) : '',
                !empty($object['price']) ? (int)$object['price'] : 0,
                $currency,
                $region,
                $area,
                $address,
                !empty($object['floor_apartment']) ? (int)$object['floor_apartment'] : null,
                !empty($object['area']) ? (float)str_replace(',', '.', $object['area']) : (!empty($object['area_snb']) ? (float)str_replace(',', '.', $object['area_snb']) : null),
                !empty($object['living_space']) ? (float)str_replace(',', '.', $object['living_space']) : null,
                !empty($object['kitchen_area']) ? (float)str_replace(',', '.', $object['kitchen_area']) : null,
                $houseType,
                $bathroom,
                $balcony,
                !empty($object['year_built']) ? (int)$object['year_built'] : null,
                $images,
                !empty($object['manager_phones']) ? $this->parsePhones(trim($object['manager_phones'])) : null,
                !empty($object['manager_name']) ? trim($object['manager_name']) : null,
                $importLink
            );
        } catch (Throwable $e) {
            Logger::log(Logger::LEVEL_FAILED, $company, 'kufar', $lot, $e->getMessage());
        }
    }

    /**
     * @param string   $company
     * @param string   $lot
     * @param mixed[]  $object
     * @param int      $region
     * @param int      $area
     * @param string   $address
     * @param string[] $images
     * @param string   $importLink
     * @return \API\Kufar\Adverts\House
     */
    protected function getHouseAdvert(
        string $company,
        string $lot,
        array $object,
        int $region,
        int $area,
        string $address,
        array $images,
        string $importLink
    ): House {
        $currency = House::findCurrencyTypeByName('$');

        if (!empty($object['land_area'])) {
            $sizeArea = round((float)str_replace(',', '.', $object['land_area']) * 100.0, 1);
        } else {
            $sizeArea = null;
        }

        $wallMaterial = House::findWallMaterialTypeByName(!empty($object['wall_material']) ? trim($object['wall_material']) : 'Кирпич');

        try {
            return new House(
                !empty($object['title']) ? trim($object['title']) : '',
                !empty($object['rooms']) ? (int)$object['rooms'] : 1,
                !empty($object['description']) ? trim($object['description']) : '',
                !empty($object['price']) ? (int)$object['price'] : 0,
                $currency,
                $region,
                $area,
                $address,
                !empty($object['area']) ? (float)str_replace(',', '.', $object['area']) : (!empty($object['area_snb']) ? (float)str_replace(',', '.', $object['area_snb']) : null),
                !empty($object['living_space']) ? (float)str_replace(',', '.', $object['living_space']) : null,
                !empty($object['kitchen_area']) ? (float)str_replace(',', '.', $object['kitchen_area']) : null,
                $sizeArea,
                !empty($object['year_built']) ? (int)$object['year_built'] : null,
                $wallMaterial,
                !empty($object['heating']),
                !empty($object['water_supply']),
                false,
                !empty($object['sewerage']),
                !empty($object['electricity']),
                false,
                false,
                $images,
                !empty($object['manager_phones']) ? $this->parsePhones(trim($object['manager_phones'])) : null,
                !empty($object['manager_name']) ? trim($object['manager_name']) : null,
                $importLink
            );
        } catch (Throwable $e) {
            Logger::log(Logger::LEVEL_FAILED, $company, 'kufar', $lot, $e->getMessage());
        }
    }

    /**
     * @param string   $company
     * @param string   $lot
     * @param mixed[]  $object
     * @param int      $region
     * @param int      $area
     * @param string   $address
     * @param string[] $images
     * @param string   $importLink
     * @return \API\Kufar\Adverts\Office
     */
    protected function getOfficeAdvert(
        string $company,
        string $lot,
        array $object,
        int $region,
        int $area,
        string $address,
        array $images,
        string $importLink
    ): Office {
        $currency = Office::findCurrencyTypeByName('$');
        $propertyType = Office::findPropertyTypeByName(!empty($object['type']) ? str_replace(['помещение', 'помещения', 'помещении'], '', trim($object['type'])) : 'Офисы');

        try {
            return new Office(
                !empty($object['title']) ? trim($object['title']) : '',
                !empty($object['transaction']) && 0 === strcmp($object['transaction'], 'аренда'),
                $propertyType,
                !empty($object['description']) ? trim($object['description']) : '',
                !empty($object['price']) ? (int)$object['price'] : (!empty($object['price_per_sqm']) ? (int)$object['price_per_sqm'] : 0),
                $currency,
                $region,
                $area,
                $address,
                !empty($object['area']) ? (float)str_replace(',', '.', $object['area']) : (!empty($object['area_snb']) ? (float)str_replace(',', '.', $object['area_snb']) : null),
                $images,
                !empty($object['manager_phones']) ? $this->parsePhones(trim($object['manager_phones'])) : null,
                !empty($object['manager_name']) ? trim($object['manager_name']) : null,
                $importLink
            );
        } catch (Throwable $e) {
            Logger::log(Logger::LEVEL_FAILED, $company, 'kufar', $lot, $e->getMessage());
        }
    }

    /**
     * @param string $phones
     * @return string[]
     */
    protected function parsePhones(string $phones): array
    {
        return array_filter(explode(',', $phones), function ($phone) {
            return preg_match('/\+375/', $phone);
        });
    }
}
