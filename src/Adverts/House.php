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
