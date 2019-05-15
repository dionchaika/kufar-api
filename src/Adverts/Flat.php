<?php

namespace API\Kufar\Adverts;

use Dionchaika\Http\Uri;
use InvalidArgumentException;
use API\Kufar\AdvertInterface;
use Psr\Http\Message\RequestInterface;
use Dionchaika\Http\Factory\RequestFactory;

/**
 * The flat advert class.
 */
class Flat implements AdvertInterface
{
    /**
     * The house type select.
     */
    const HOUSE_TYPE = [

        1 => 'Панельный',
        2 => 'Монолитный',
        3 => 'Кирпичный',
        4 => 'Блочный',
        5 => 'Каркасный'

    ];

    /**
     * The bathroom type select.
     */
    const BATHROOM_TYPE = [

        1 => 'Раздельный',
        2 => 'Совмещенный',
        3 => 'Два',
        4 => 'Три'

    ];

    /**
     * The balcony type select.
     */
    const BALCONY_TYPE = [

        1 => 'Есть',
        2 => 'Нет',
        3 => 'Лоджия',
        4 => 'Два'

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
     * of flat advert data.
     *
     * @var mixed[]
     */
    protected $data = [

        'ad' => [

            'is_new_image'      => [true],
            'language'          => 'ru',
            'subject'           => null,
            'category'          => 1010,
            'type'              => 'sell',
            'rooms'             => null,
            'floor'             => null,
            'size'              => null,
            'size_living_space' => null,
            'size_kitchen'      => null,
            'house_type'        => null,
            'bathroom'          => null,
            'balcony'           => null,
            'year_built'        => null,
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
     * @param string      $street
     * @param string[]    $phones
     * @param int|null    $floor
     * @param float|null  $size
     * @param float|null  $sizeLivingSpace
     * @param float|null  $sizeKitchen
     * @param int|null    $houseType
     * @param int|null    $bathroom
     * @param int|null    $balcony
     * @param int|null    $yearBuilt
     * @param string[]    $images
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
        string $street,
        array $phones,
        ?int $floor = null,
        ?float $size = null,
        ?float $sizeLivingSpace = null,
        ?float $sizeKitchen = null,
        ?int $houseType = null,
        ?int $bathroom = null,
        ?int $balcony = null,
        ?int $yearBuilt = null,
        array $images = [],
        ?string $contactPerson = null,
        ?string $importLink = null
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

        if (empty($phones)) {
            throw new InvalidArgumentException(
                'Required field is not defined or empty: phones!'
            );
        }

        $phones = array_map(function ($phone) {
            return preg_replace('/[^\d]/', '', $phone);
        }, $phones);

        $this->data['ad']['subject']           = $subject;
        $this->data['ad']['rooms']             = $rooms;
        $this->data['ad']['body']              = $body;
        $this->data['ad']['price']             = $price;
        $this->data['ad']['currency']          = $currency;
        $this->data['ad']['region']            = $region;
        $this->data['ad']['area']              = $area;
        $this->data['ad']['phone']             = implode(',', $phones);
        $this->data['ad']['floor']             = $floor;
        $this->data['ad']['size']              = $size;
        $this->data['ad']['size_living_space'] = $sizeLivingSpace;
        $this->data['ad']['size_kitchen']      = $sizeKitchen;
        $this->data['ad']['house_type']        = $houseType;
        $this->data['ad']['bathroom']          = $bathroom;
        $this->data['ad']['balcony']           = $balcony;
        $this->data['ad']['year_built']        = $yearBuilt;
        $this->data['ad']['contact_person']    = $contactPerson;
        $this->data['ad']['import_link']       = $importLink;
    }

    /**
     * Get the HTTP request for the flat advert.
     *
     * @return \Psr\Http\Message\RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        $uri = new Uri('https://www.kufar.by/listings/');
        return (new RequestFactory)
            ->createJsonRequest('POST', $uri, $this->data, [
                JSON_NUMERIC_CHECK,
                JSON_UNESCAPED_SLASHES,
                JSON_UNESCAPED_UNICODE,
                JSON_PRETTY_PRINT
            ]);
    }
}
