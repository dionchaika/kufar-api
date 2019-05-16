<?php

namespace API\Kufar\Adverts;

use Dionchaika\Http\Uri;
use API\Kufar\AdvertInterface;
use Psr\Http\Message\RequestInterface;
use Dionchaika\Http\Factory\RequestFactory;

/**
 * The house advert class.
 */
class House implements AdvertInterface
{
    /**
     * The wall material type select.
     */
    const WALL_MATERIAL_TYPE = [

        1 => 'Кирпич',
        2 => 'Дерево',
        3 => 'Дерево, обложено кирпичом',
        4 => 'Сборно-щитовой',
        5 => 'Блочный',
        6 => 'Керамзитбетон',
        7 => 'Шлакобетон',
        8 => 'Панельный',
        9 => 'Другой'

    ];

    /**
     * The array
     * of house advert data.
     *
     * @var mixed[]
     */
    protected $data = [

        'ad' => [

            'is_new_image'      => [],
            'language'          => 'ru',
            'subject'           => null,
            'category'          => 1020,
            'type'              => 'sell',
            'rooms'             => null,
            'size'              => null,
            'size_living_space' => null,
            'size_kitchen'      => null,
            'size_area'         => null,
            'year_built'        => null,
            'wall_material'     => null,
            'heating'           => null,
            'water'             => null,
            'gaz'               => null,
            'sewage'            => null,
            'electricity'       => null,
            'pond_river'        => null,
            'banya'             => null,
            'condition'         => 1,
            'body'              => null,
            'price'             => null,
            'currency'          => null,
            'region'            => null,
            'area'              => null,
            'address'           => null,
            'name'              => null,
            'email'             => null,
            'phone'             => null,
            'contact_person'    => null,
            'company_address'   => null,
            'import_link'       => null,
            'vat_number'        => null,
            'company_number'    => null,
            'company_ad'        => null,
            'coordinates'       => null,
            'address_tags'      => null,
            'remuneration_type' => 1,
            'images'            => [],
            'delivery'          => null

        ]

    ];

    /**
     * @param string      $subject
     * @param int         $rooms
     * @param string      $body
     * @param int         $price
     * @param string      $currency
     * @param int         $region
     * @param int         $area
     * @param string      $address
     * @param float|null  $size
     * @param float|null  $sizeLivingSpace
     * @param float|null  $sizeKitchen
     * @param float|null  $sizeArea
     * @param int|null    $yearBuilt
     * @param int|null    $wallMaterial
     * @param bool        $heating
     * @param bool        $water
     * @param bool        $gas
     * @param bool        $sewage
     * @param bool        $electricity
     * @param bool        $pondRiver
     * @param bool        $banya
     * @param string[]    $images
     * @param string[]    $phones
     * @param string|null $contactPerson
     * @param string|null $importLink
     * @throws \InvalidArgumentException
     */
    public function __construct(
        string $subject,
        int $rooms,
        string $body,
        int $price,
        string $currency,
        int $region,
        int $area,
        string $address,
        ?float $size            = null,
        ?float $sizeLivingSpace = null,
        ?float $sizeKitchen     = null,
        ?float $sizeArea        = null,
        ?int $yearBuilt         = null,
        ?int $wallMaterial      = null,
        bool $heating           = false,
        bool $water             = false,
        bool $gas               = false,
        bool $sewage            = false,
        bool $electricity       = false,
        bool $pondRiver         = false,
        bool $banya             = false,
        array $images           = [],
        array $phones           = [],
        ?string $contactPerson  = null,
        ?string $importLink     = null
    ) {
        if ('' === $subject) {
            throw new InvalidArgumentException(
                'Required field is not defined or empty: subject!'
            );
        }

        $subject = mb_substr($subject, 0, 50);

        if (20 > mb_strlen($body)) {
            throw new InvalidArgumentException(
                'Required field is not defined or empty: body!'
            );
        }

        $body = mb_substr($body, 0, 4000);

        if (5 < $rooms) {
            $rooms = 5;
        }

        if (1980 > $yearBuilt) {
            $yearBuilt = 1980;
        } else if (2025 < $yearBuilt) {
            $yearBuilt = 2025;
        }

        if (15 < count($images)) {
            $images = array_splice($images, 15);
        }

        $phones = array_map(function ($phone) {
            return preg_replace('/[^\d]/', '', $phone);
        }, $phones);

        if (3 < count($phones)) {
            $phones = array_splice($phones, 3);
        }

        $this->data['ad']['subject']           = $subject;
        $this->data['ad']['rooms']             = $rooms;
        $this->data['ad']['body']              = $body;
        $this->data['ad']['price']             = $price;
        $this->data['ad']['currency']          = $currency;
        $this->data['ad']['region']            = $region;
        $this->data['ad']['area']              = $area;
        $this->data['ad']['address']           = $address;
        $this->data['ad']['size']              = $size;
        $this->data['ad']['size_living_space'] = $sizeLivingSpace;
        $this->data['ad']['size_kitchen']      = $sizeKitchen;
        $this->data['ad']['size_area']         = $sizeArea;
        $this->data['ad']['year_built']        = $yearBuilt;
        $this->data['ad']['wall_material']     = $wallMaterial;
        $this->data['ad']['heating']           = $heating ? 1 : null;
        $this->data['ad']['water']             = $water ? 1 : null;
        $this->data['ad']['gas']               = $gas ? 1 : null;
        $this->data['ad']['sewage']            = $sewage ? 1 : null;
        $this->data['ad']['electricity']       = $electricity ? 1 : null;
        $this->data['ad']['pond_river']        = $pondRiver ? 1 : null;
        $this->data['ad']['banya']             = $banya ? 1 : null;
        $this->data['ad']['images']            = $images;
        $this->data['ad']['phone']             = implode(',', $phones);
        $this->data['ad']['contact_person']    = $contactPerson;
        $this->data['ad']['import_link']       = $importLink;
    }

    /**
     * Find the wall material type by name.
     *
     * @param string $wallMaterialTypeName
     * @return int
     * @throws \InvalidArgumentException
     */
    public static function findWallMaterialTypeByName(string $wallMaterialTypeName): int
    {
        foreach (static::WALL_MATERIAL_TYPE as $key => $value) {
            if (preg_match('/'.$wallMaterialTypeName.'/i', $value)) {
                return $key;
            }
        }

        throw new InvalidArgumentException(
            'Undefined wall material type name: '.$wallMaterialTypeName.'!'
        );
    }

    /**
     * Set an account info.
     *
     * @param mixed[] $accountInfo
     * @return self
     */
    public function setAccountInfo(array $accountInfo): self
    {
        $this->data['ad']['name']            = $accountInfo['name'];
        $this->data['ad']['email']           = $accountInfo['email'];
        $this->data['ad']['company_address'] = $accountInfo['company_address'];
        $this->data['ad']['vat_number']      = $accountInfo['vat_number'];
        $this->data['ad']['company_number']  = $accountInfo['company_number'];
        $this->data['ad']['company_ad']      = $accountInfo['company_ad'];

        if (null === $this->data['ad']['phone']) {
            $this->data['ad']['phone'] = $accountInfo['phone'];
        }

        if (null === $this->data['ad']['contact_person']) {
            $this->data['ad']['contact_person'] = $accountInfo['contact_person'];
        }

        return $this;
    }

    /**
     * Set an address info.
     *
     * @param array $addressInfo
     * @return self
     */
    public function setAddressInfo(array $addressInfo): self
    {
        $this->data['ad']['coordinates']  = $addressInfo['coordinates'];
        $this->data['ad']['address_tags'] = $addressInfo['address_tags'];

        return $this;
    }

    /**
     * Get the HTTP request for the house advert.
     *
     * @return \Psr\Http\Message\RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        $data = $this->data;
        foreach ($data['ad'] as $key => $value) {
            if (null === $value) {
                unset($data['ad'][$key]);
            }
        }

        $uri = new Uri('https://www.kufar.by/react/api/cre/ad-insertion/v1/processing/insert');
        return (new RequestFactory)
            ->createJsonRequest('POST', $uri, $data, [\JSON_NUMERIC_CHECK, \JSON_UNESCAPED_SLASHES, \JSON_UNESCAPED_UNICODE, \JSON_PRETTY_PRINT])
            ->withHeader('X-segmentation', 'routing=web_ad_insertion;application=ad_insertion;platform=web');
    }
}
