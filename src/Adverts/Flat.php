<?php

namespace API\Kufar\Adverts;

use Dionchaika\Http\Uri;
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
            'company_ad'        => 1,
            'coordinates'       => null,
            'address_tags'      => null,
            'remuneration_type' => 1,
            'images'            => [],
            'delivery'          => null

        ]

    ];

    /**
     * Get the HTTP request for the flat advert.
     *
     * @return \Psr\Http\Message\RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        $uri = new Uri('https://www.kufar.by/listings/');
        return (new RequestFactory)
            ->createJsonRequest('POST', $uri, $this->data);
    }
}
