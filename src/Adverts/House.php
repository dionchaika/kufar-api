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
     * The array
     * of house advert data.
     *
     * @var mixed[]
     */
    protected $data = [

        'ad' => [

            

        ]

    ];

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
