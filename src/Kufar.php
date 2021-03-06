<?php

namespace API\Kufar;

use RuntimeException;
use Dionchaika\Http\Uri;
use InvalidArgumentException;
use Dionchaika\Http\Client\Client;
use Dionchaika\Http\Utils\FormData;
use Dionchaika\Http\Factory\RequestFactory;
use Psr\Http\Client\ClientExceptionInterface;

/**
 * The API class for www.kufar.by.
 */
class Kufar
{
    /**
     * The address region select.
     */
    const REGION = [

        7 => 'Минск',
        1 => 'Брестская',
        2 => 'Гомельская',
        3 => 'Гродненская',
        4 => 'Могилевская',
        5 => 'Минская',
        6 => 'Витебская'

    ];

    /**
     * The address area select.
     */
    const AREA = [

        7 => [

            22 => 'Центральный',
            23 => 'Советский',
            24 => 'Первомайский',
            25 => 'Партизанский',
            26 => 'Заводской',
            27 => 'Ленинский',
            28 => 'Октябрьский',
            29 => 'Московский',
            30 => 'Фрунзенский'

        ],

        1 => [

            1   => 'Брест',
            37  => 'Барановичи',
            38  => 'Береза',
            123 => 'Белоозёрск',
            48  => 'Ганцевичи',
            49  => 'Дрогичин',
            50  => 'Жабинка',
            51  => 'Иваново',
            52  => 'Ивацевичи',
            53  => 'Каменец',
            2   => 'Кобрин',
            3   => 'Лунинец',
            54  => 'Ляховичи',
            55  => 'Малорита',
            4   => 'Пинск',
            56  => 'Пружаны',
            57  => 'Столин',
            31  => 'Другие города'

        ],

        2 => [

            5   => 'Гомель',
            128 => 'Брагин',
            58  => 'Буда-Кошелево',
            59  => 'Ветка',
            60  => 'Добруш',
            61  => 'Ельск',
            62  => 'Житковичи',
            6   => 'Жлобин',
            63  => 'Калинковичи',
            129 => 'Корма',
            130 => 'Лельчицы',
            131 => 'Лоев',
            7   => 'Мозырь',
            132 => 'Октябрьский',
            64  => 'Наровля',
            65  => 'Петриков',
            8   => 'Речица',
            66  => 'Рогачев',
            39  => 'Светлогорск',
            67  => 'Хойники',
            68  => 'Чечерск',
            32  => 'Другие города'

        ],

        3 => [

            9   => 'Гродно',
            69  => 'Березовка',
            133 => 'Берестовица',
            40  => 'Волковыск',
            134 => 'Вороново',
            70  => 'Дятлово',
            135 => 'Зельва',
            71  => 'Ивье',
            136 => 'Кореличи',
            10  => 'Лида',
            72  => 'Мосты',
            73  => 'Новогрудок',
            74  => 'Островец',
            75  => 'Ошмяны',
            76  => 'Свислочь',
            77  => 'Скидель',
            11  => 'Слоним',
            41  => 'Сморгонь',
            78  => 'Щучин',
            33  => 'Другие города'

        ],

        4 => [

            13  => 'Могилев',
            137 => 'Белыничи',
            12  => 'Бобруйск',
            79  => 'Быхов',
            80  => 'Глуск',
            42  => 'Горки',
            138 => 'Дрибин',
            81  => 'Кировск',
            82  => 'Климовичи',
            83  => 'Кличев',
            139 => 'Краснополье',
            140 => 'Круглое',
            84  => 'Костюковичи',
            43  => 'Кричев',
            85  => 'Мстиславль',
            14  => 'Осиповичи',
            86  => 'Славгород',
            87  => 'Чаусы',
            88  => 'Чериков',
            89  => 'Шклов',
            141 => 'Хотимск',
            34  => 'Другие города'

        ],

        5 => [

            142 => 'Минский район',
            91  => 'Березино',
            15  => 'Борисов',
            92  => 'Вилейка',
            93  => 'Воложин',
            94  => 'Дзержинск',
            44  => 'Жодино',
            143 => 'Заславль',
            95  => 'Клецк',
            96  => 'Копыль',
            97  => 'Крупки',
            98  => 'Логойск',
            99  => 'Любань',
            122 => 'Марьина Горка',
            16  => 'Молодечно',
            100 => 'Мядель',
            101 => 'Несвиж',
            145 => 'Руденск',
            17  => 'Слуцк',
            102 => 'Смолевичи',
            45  => 'Солигорск',
            103 => 'Старые Дороги',
            104 => 'Столбцы',
            105 => 'Узда',
            144 => 'Фаниполь',
            106 => 'Червень',
            35  => 'Другие города'

        ],

        6 => [

            18  => 'Витебск',
            125 => 'Бешенковичи',
            107 => 'Барань',
            108 => 'Браслав',
            109 => 'Верхнедвинск',
            110 => 'Глубокое',
            111 => 'Городок',
            112 => 'Докшицы',
            113 => 'Дубровно',
            114 => 'Лепель',
            115 => 'Лиозно',
            116 => 'Миоры',
            117 => 'Новолукомль',
            46  => 'Новополоцк',
            19  => 'Орша',
            20  => 'Полоцк',
            47  => 'Поставы',
            118 => 'Россоны',
            119 => 'Сенно',
            120 => 'Толочин',
            126 => 'Ушачи',
            121 => 'Чашники',
            127 => 'Шарковщина',
            124 => 'Шумилино',
            36  => 'Другие города'

        ]

    ];

    /**
     * The HTTP client.
     *
     * @var \Dionchaika\Http\Client\Client
     */
    protected $client;

    /**
     * The HTTP request factory.
     *
     * @var \Dionchaika\Http\Factory\RequestFactory
     */
    protected $factory;

    /**
     * Is the client logged in.
     *
     * @var bool
     */
    protected $loggedIn = false;

    /**
     * The API constructor.
     *
     * @param  bool  $debug
     * @param  string|null  $debugFile
     */
    public function __construct(bool $debug = false, ?string $debugFile = null)
    {
        $config = [

            'headers' => [

                'Accept'          => 'text/html, application/xhtml+xml, application/xml; q=0.9, image/webp, image/apng, */*; q=0.8, application/signed-exchange; v=b3',
                'Accept-Encoding' => 'gzip, deflate',
                'Accept-Language' => 'ru-RU, ru; q=0.9, en-US; q=0.8, en; q=0.7',

                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.157 Safari/537.36'

            ],

            'redirects' => true,

            'debug'      => $debug,
            'debug_file' => $debugFile

        ];

        $this->client = new Client($config);
        $this->factory = new RequestFactory;
    }

    /**
     * Find the address region by name.
     *
     * @param  string  $regionName
     * @return int
     */
    public static function findRegionByName(string $regionName): int
    {
        return Finder::suggestKey($regionName, static::REGION);
    }

    /**
     * Find the address area by name.
     *
     * @param  int  $region
     * @param  string  $areaName
     * @return int
     */
    public static function findAreaByName(int $region, string $areaName): int
    {
        if (! array_key_exists($region, static::AREA)) {
            throw new InvalidArgumentException('Invalid region!');
        }

        return Finder::suggestKey($areaName, static::AREA[$region]);
    }

    /**
     * Log in.
     *
     * @param  string  $user
     * @param  string  $password
     * @return void
     *
     * @throws \RuntimeException
     */
    public function login(string $user, string $password): void
    {
        $uri = new Uri('https://www.kufar.by/listings/');
        try {
            $response = $this->client->sendRequest($this->factory->createRequest('GET', $uri));
        } catch (ClientExceptionInterface $e) {
            throw new RuntimeException($e->getMessage());
        }

        if (200 !== $response->getStatusCode()) {
            throw new RuntimeException('Error loading page: '.$uri.'!');
        }

        $data = [

            'email'    => $user,
            'password' => $password

        ];

        $uri = new Uri('https://www.kufar.by/react/api/login/v1/auth/signin?token_type=user');
        try {
            $response = $this->client->sendRequest($this->factory->createJsonRequest('POST', $uri, $data));
        } catch (ClientExceptionInterface $e) {
            throw new RuntimeException($e->getMessage());
        }

        if (200 !== $response->getStatusCode()) {
            throw new RuntimeException('Login error!');
        }

        $this->loggedIn = true;
    }

    /**
     * Log out.
     *
     * @return void
     */
    public function logout(): void
    {
        $this->loggedIn = false;
        $this->client->getCookieStorage()->clearSessionCookies();
    }

    /**
     * Post an advert.
     *
     * Return data example:
     *      <code>
     *          // On failed:
     *          [
     *
     *              'code'    => 'ADINS0001',
     *              'message' => 'ad validation failed',
     *              'details' => [
     *
     *                  'fields' => [
     *
     *                      'rooms' => [
     *
     *                          'message'      => 'ERROR_CONTENT_INVALID',
     *                          'translations' => [
     *
     *                              'ru' => 'Введите корректное значение',
     *                              'by' => 'Увядзіце карэктнае значэнне'
     *
     *                          ]
     *
     *                      ]
     *
     *                  ]
     *
     *              ],
     *              'http' => [
     *
     *                  'message' => 'Unprocessable Entity',
     *                  'code'    => 422
     *
     *              ]
     *
     *          ]
     *
     *          // On success:
     *          [
     *
     *              'ad_id'        => 12345678,
     *              'paycode'      => 123456789,
     *              'pending_slot' => null
     *
     *          ]
     *      </code>
     *
     * @param  \API\Kufar\AdvertInterface  $advert
     * @return mixed[]
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function postAdvert(AdvertInterface $advert): array
    {
        if (! $this->loggedIn) {
            throw new RuntimeException('Client is not logged in!');
        }

        $uri = new Uri('https://www.kufar.by/ain/create');
        try {
            $response = $this->client->sendRequest($this->factory->createRequest('GET', $uri));
        } catch (ClientExceptionInterface $e) {
            throw new RuntimeException($e->getMessage());
        }

        if (200 !== $response->getStatusCode()) {
            throw new RuntimeException('Error loading page: '.$uri.'!');
        }

        try {
            $response = $this->client->sendRequest($advert->getRequest());
        } catch (ClientExceptionInterface $e) {
            throw new RuntimeException($e->getMessage());
        }

        return json_decode($response->getBody(), \JSON_OBJECT_AS_ARRAY);
    }

    /**
     * Get the adverts.
     *
     * @return mixed[]
     *
     * @throws \RuntimeException
     */
    public function getAdverts(): array
    {
        if (! $this->loggedIn) {
            throw new RuntimeException('Client is not logged in!');
        }

        $published = $deactivated = [];

        $uri = new Uri('https://www.kufar.by/account/my_ads/published');
        try {
            $response = $this->client->sendRequest($this->factory->createRequest('GET', $uri));
        } catch (ClientExceptionInterface $e) {
            throw new RuntimeException($e->getMessage());
        }

        if (200 !== $response->getStatusCode()) {
            throw new RuntimeException('Error loading page: '.$uri.'!');
        }

        $html = str_get_html($response->getBody());
    }

    /**
     * Upload an image.
     *
     * Return data example:
     *      <code>
     *          [
     *
     *              'index'    => 0,
     *              'img_link' => '3110656605.jpg'
     *
     *          ]
     *      </code>
     *
     * @param  string  $filename
     * @return mixed[]
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function uploadImage(string $filename): array
    {
        if (! $this->loggedIn) {
            throw new RuntimeException('Client is not logged in!');
        }

        if (! file_exists($filename)) {
            throw new InvalidArgumentException('File does not exists: '.$filename.'!');
        }

        if (10485760 < filesize($filename)) {
            throw new InvalidArgumentException('File size can not be greater than 10 MB!');
        }

        $formData = (new FormData)
            ->append('images[]', '@'.$filename)
            ->append('application', 'ad_insertion');

        $uri = new Uri('https://www2.kufar.by/image_uploader');
        try {
            $response = $this->client->sendRequest($this->factory->createFormDataRequest('POST', $uri, $formData));
        } catch (ClientExceptionInterface $e) {
            throw new RuntimeException($e->getMessage());
        }

        if (200 !== $response->getStatusCode()) {
            throw new RuntimeException('Error uploading image!');
        }

        return json_decode($response->getBody(), \JSON_OBJECT_AS_ARRAY)[0];
    }

    /**
     * Get the account info.
     *
     * Return data example:
     *      <code>
     *          [
     *
     *              'account_id'             => '',
     *              'area'                   => '',
     *              'company_ad'             => '',
     *              'company_address'        => '',
     *              'company_number'         => '',
     *              'contact_person'         => '',
     *              'email'                  => '',
     *              'name'                   => '',
     *              'origin'                 => '',
     *              'partner'                => '',
     *              'partner_link'           => '',
     *              'partner_supervisor_ids' => '',
     *              'partner_text'           => '',
     *              'partner_type'           => '',
     *              'phone'                  => '',
     *              'phone_hidden'           => '',
     *              'profile_image'          => '',
     *              'region'                 => '',
     *              'sales_email'            => '',
     *              'shop_address'           => '',
     *              'should_verify_phone'    => '',
     *              'token'                  => '',
     *              'vat_number'             => '',
     *              'verified_phone'         => '',
     *              'web_shop_link'          => ''
     *
     *          ]
     *      </code>
     *
     * @return mixed[]
     *
     * @throws \RuntimeException
     */
    public function getAccountInfo(): array
    {
        if (! $this->loggedIn) {
            throw new RuntimeException('Client is not logged in!');
        }

        $uri = new Uri('https://www.kufar.by/react/api/user?apiName=account_info');
        try {
            $response = $this->client->sendRequest($this->factory->createRequest('GET', $uri));
        } catch (ClientExceptionInterface $e) {
            throw new RuntimeException($e->getMessage());
        }

        if (200 !== $response->getStatusCode()) {
            throw new RuntimeException('Error getting account info!');
        }

        return json_decode($response->getBody(), \JSON_OBJECT_AS_ARRAY);
    }

    /**
     * Get the address info.
     *
     * Return data example:
     *      <code>
     *          [
     *
     *              'coordinates'  => '52.085926,23.7038141',
     *              'address_tags' => 'o-brestskaja-oblast,o-brestskaja-oblast-c-brest'
     *
     *          ]
     *      </code>
     *
     * @param  int  $region
     * @param  int  $area
     * @param  string  $address
     * @return mixed[]
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function getAddressInfo(int $region, int $area, string $address): array
    {
        if (! $this->loggedIn) {
            throw new RuntimeException('Client is not logged in!');
        }

        if (! array_key_exists($region, self::REGION)) {
            throw new InvalidArgumentException('Invalid region!');
        }

        $regionName = self::REGION[$region];

        if (! array_key_exists($area, self::AREA[$region])) {
            throw new InvalidArgumentException('Invalid area!');
        }

        $areaName = self::AREA[$region][$area];

        $uri = (new Uri('https://geocoder.kufar.by/search/get_suggestions'))
            ->withQuery('city='.urlencode($areaName).'&query='.urlencode($address).'&region='.urlencode($regionName));
        try {
            $response = $this->client->sendRequest($this->factory->createRequest('GET', $uri));
        } catch (ClientExceptionInterface $e) {
            throw new RuntimeException($e->getMessage());
        }

        if (200 !== $response->getStatusCode()) {
            throw new RuntimeException('Error getting address info!');
        }

        $data = json_decode($response->getBody(), \JSON_OBJECT_AS_ARRAY);

        return [

            'coordinates'  => implode(',', $data['data'][0]['coordinates']),
            'address_tags' => implode(',', $data['data'][0]['tags'])

        ];
    }
}
