<?php

namespace App\Tests\Controller;

use App\Service\ApiV1;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiV1ControllerTest extends WebTestCase
{
    /**
     * @var KernelBrowser
     */
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    /**
     * @dataProvider additionProvider
     *
     * @param string $method
     * @param array $server
     * @param $content
     */
    public function testUnsuccessful($method, $server, $content)
    {
        $this->client->request($method, '/v1/api/add', [], [],
            $server,
            $content
        );

        $this->assertResponseIsSuccessful();
        $results = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($results);
        $this->assertArrayHasKey("success", $results);
        $this->assertSame($results["success"], ApiV1::RESULT_FAIL);
    }

    /**
     * @dataProvider additionAllowedProvider
     *
     * @param string $method
     * @param string $uri
     */
    public function testNotAllowedMethod($method, $uri)
    {
        $this->client->request($method, $uri);
        $this->assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    /**
     * @dataProvider additionAddProvider
     *
     * @param $content
     */
    public function testAddEventsFirst($content)
    {
        $this->addEvents($content);
        sleep(1);
    }

    /**
     * @dataProvider additionRandomProvider
     *
     * @param array $parameters
     */
    public function testRandom($parameters)
    {
        $this->client->request(Request::METHOD_GET, '/v1/api/random', $parameters);

        $this->assertResponseIsSuccessful();

        $results = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($results);
        $this->assertLessThan(count($results), 0);
        $this->assertArrayHasKey("game", $results);
        $this->assertArrayHasKey("buffers", $results);
        $this->assertLessThan(count($results["game"]), 0);
        $this->assertLessThan(count($results["buffers"]), 0);
    }

    /**
     * @depends testRandom
     */
    public function testAddEventsSecond()
    {
        $content =
            '{"events": [
                {
                    "lang": "русский",
                    "sport": "Баскетбол",
                    "league": "Суперлига 1",
                    "team1": "Урал",
                    "team2": "Автодор",
                    "date": "2020-03-01 10:00:00",
                    "source": "sportdata.com"
                }
            ]}';
        $this->addEvents($content);
    }

    /**
     * @param $content
     */
    public function addEvents($content)
    {
        $this->client->request(Request::METHOD_POST, '/v1/api/add', [], [],
            ['CONTENT_TYPE' => 'application/json'],
            $content
        );
        $this->assertResponseIsSuccessful();

        $results = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($results);
        $this->assertArrayHasKey("success", $results);
        $this->assertSame($results["success"], ApiV1::RESULT_SUCCESS);
    }

    public function additionProvider()
    {
        yield [Request::METHOD_POST, ['CONTENT_TYPE' => 'application/json'], '{}'];
        yield [Request::METHOD_POST, ['CONTENT_TYPE' => 'application/json'], null];
        yield [Request::METHOD_POST, ['CONTENT_TYPE' => 'application/json'], '{"events": []}'];
        yield [Request::METHOD_POST, ['CONTENT_TYPE' => 'text/html'], null];
        yield [Request::METHOD_POST, ['CONTENT_TYPE' => 'application/json'], '{"events": ["lang": "русский"]}'];
    }

    public function additionAllowedProvider()
    {
        yield [Request::METHOD_GET, '/v1/api/add'];
        yield [Request::METHOD_POST, '/v1/api/random'];
    }

    public function additionAddProvider()
    {
        yield [
            '{"events": [
                {
                    "lang": "русский",
                    "sport": "Баскетбол",
                    "league": "Евролига",
                    "team1": "ЦСКА",
                    "team2": "Панатинаикос",
                    "date": "2020-02-01 10:00:00",
                    "source": "sportdata.com"
                }
            ]}'
        ];
        yield [
            '{"events": [
                {
                    "lang": "русский",
                    "sport": "Баскетбол",
                    "league": "Евролига",
                    "team1": "ЦСКА",
                    "team2": "Панатинаикос",
                    "date": "2020-02-01 10:00:00",
                    "source": "sportdata1.com"
                },
                {
                    "lang": "русский",
                    "sport": "Хоккей",
                    "league": "КХЛ",
                    "team1": "СКА",
                    "team2": "Сибирь",
                    "date": "2020-06-13 18:00:00",
                    "source": "sportdata3.com"
                },
                {
                    "lang": "русский",
                    "sport": "Хоккей",
                    "league": "КХЛ",
                    "team1": "СКА",
                    "team2": "Сибирь",
                    "date": "2020-06-13 18:00:00",
                    "source": "sportdata1.com"
                }
            ]}'
        ];
    }

    public function additionRandomProvider()
    {
        yield [
            []
        ];
        yield [
            ['source' => 'sportdata1.com']
        ];
        yield [
            [
                'start' => '2020-01-00 10:00:00',
                'end' => '2020-10-01 10:00:00'
            ]
        ];
        yield [
            [
                'source' => 'sportdata1.com',
                'start' => '2020-01-00 10:00:00',
                'end' => '2020-10-01 10:00:00'
            ]
        ];
    }
}