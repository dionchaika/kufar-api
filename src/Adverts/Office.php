<?php

namespace API\Kufar\Adverts;

use Dionchaika\Http\Uri;
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



        ]

    ];

    /**
     * Get the HTTP request for the office advert.
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
