<?php

namespace API\Kufar;

use Psr\Http\Message\RequestInterface;

/**
 * The advert interface.
 */
interface AdvertInterface
{
    /**
     * Get the HTTP request for the advert.
     *
     * @return \Psr\Http\Message\RequestInterface
     */
    public function getRequest(): RequestInterface;
}
