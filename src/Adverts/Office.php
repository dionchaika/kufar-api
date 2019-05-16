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
     * The array
     * of office advert data.
     *
     * @var mixed[]
     */
    protected $data = [

        'ad' => [

            'is_new_image' => [],
            'language' => 'ru',
            'category' => 1050,
            'type' => 'sell',
            'size' => null,
            'property_type' => null,
            'condition' => 1,
            'body' => null,
            'price' => null,
            'currency' => null,
            'region' => null,
            'area' => null,
            'address' => null,
            'name' => null,
            'email' => null,
            'phone' => null,
            'contact_person' => null,
            'company_address' => null,
            'import_link' => null,
            'vat_number' => null,
            'company_number' => null,
            'company_ad' => null,
            'coordinates' => null,
            'address_tags' => null,
            'remuneration_type' => 1,
            'images' => []

        ],
        'delivery' => null

    ];

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
