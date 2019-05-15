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
        $uri = new Uri('https://www.kufar.by/listings/');
        return (new RequestFactory)
            ->createJsonRequest('POST', $uri, $this->data);
    }
}
