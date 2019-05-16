<?php

namespace API\Kufar\Adverts;

use API\Kufar\Finder;
use Dionchaika\Http\Uri;
use InvalidArgumentException;
use API\Kufar\AdvertInterface;
use Psr\Http\Message\RequestInterface;
use Dionchaika\Http\Factory\RequestFactory;

/**
 * The office advert class.
 */
class Office implements AdvertInterface
{
    /**
     * The property type select.
     */
    const PROPERTY_TYPE = [

        1 => 'Офисы',
        2 => 'Торговые павильоны',
        3 => 'Промышленные помещения',
        4 => 'Склады',
        6 => 'Прочая коммерческая'

    ];

    /**
     * The currency type select.
     */
    const CURRENCY_TYPE = [

        'BYR' => 'р.',
        'USD' => '$',
        'EUR' => '€'

    ];

    /**
     * The array
     * of office advert data.
     *
     * @var mixed[]
     */
    protected $data = [

        'ad' => [

            'is_new_image'      => [],
            'language'          => 'ru',
            'subject'           => null,
            'category'          => 1050,
            'type'              => 'sell',
            'size'              => null,
            'property_type'     => null,
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
            'images'            => []

        ],
        'delivery' => null

    ];

    /**
     * @param string      $subject
     * @param bool        $rent
     * @param int         $propertyType
     * @param string      $body
     * @param int         $price
     * @param string      $currency
     * @param int         $region
     * @param int         $area
     * @param string      $address
     * @param float|null  $size
     * @param string[]    $images
     * @param string[]    $phones
     * @param string|null $contactPerson
     * @param string|null $importLink
     * @throws \InvalidArgumentException
     */
    public function __construct(
        string $subject,
        bool $rent              = false,
        int $propertyType,
        string $body,
        int $price,
        string $currency,
        int $region,
        int $area,
        string $address,
        ?float $size            = null,
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

        if (15 < count($images)) {
            $images = array_splice($images, 15);
        }

        $phones = array_map(function ($phone) {
            return preg_replace('/[^\d]/', '', $phone);
        }, $phones);

        if (3 < count($phones)) {
            $phones = array_splice($phones, 3);
        }

        if ($rent) {
            $this->data['ad']['type'] = 'let';
        }

        $this->data['ad']['subject']        = $subject;
        $this->data['ad']['property_type']  = $propertyType;
        $this->data['ad']['body']           = $body;
        $this->data['ad']['price']          = $price;
        $this->data['ad']['currency']       = $currency;
        $this->data['ad']['region']         = $region;
        $this->data['ad']['area']           = $area;
        $this->data['ad']['address']        = $address;
        $this->data['ad']['size']           = $size;
        $this->data['ad']['images']         = $images;
        $this->data['ad']['phone']          = implode(',', $phones);
        $this->data['ad']['contact_person'] = $contactPerson;
        $this->data['ad']['import_link']    = $importLink;
    }

    /**
     * Find the property type by name.
     *
     * @param string $propertyTypeName
     * @return int
     */
    public static function findpropertyTypeByName(string $propertyTypeName): int
    {
        return Finder::find($propertyTypeName, static::PROPERTY_TYPE);
    }

    /**
     * Find the currency type by name.
     *
     * @param string $currencyTypeName
     * @return string
     */
    public static function findCurrencyTypeByName(string $currencyTypeName): string
    {
        return Finder::find($currencyTypeName, static::CURRENCY_TYPE);
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
     * Get the HTTP request for the office advert.
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
