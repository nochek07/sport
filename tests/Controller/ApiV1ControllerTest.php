<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiV1ControllerTest extends WebTestCase
{
    public function testAdd()
    {
        $client = static::createClient();
        $client->request('POST', '/v1/api/add', [], [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"events": [
                {
                    "lang": "русский",
                    "sport": "Баскетбол",
                    "league": "Евролига",
                    "team1": "ЦСКА",
                    "team2": "Панатинаикос",
                    "date": "2020-01-01 10:01:01",
                    "source": "sportdata.com"
                }
            ]}'
        );

        $this->assertResponseIsSuccessful();

        $results = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($results);
        $this->assertArrayHasKey("success", $results);
    }
}