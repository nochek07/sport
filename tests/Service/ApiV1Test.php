<?php

namespace App\Tests\Service;

use App\DTO\GameBufferDTO;
use App\Service\ApiV1;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiV1Test extends KernelTestCase
{

    /**
     * @var ApiV1
     */
    private $stubApiV1;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function setUp(): void
    {
        self::bootKernel();
        
        $container = self::$container;
        $this->validator = $container->get("validator");
        $this->serializer = $container->get("serializer");

        $this->stubApiV1 = $this->createMock(ApiV1::class);
    }

    /**
     * @dataProvider additionValidDTOProvider
     *
     * @param int $size
     * @param array $events
     */
    public function testCheckValidData(int $size, array $events)
    {
        $class = new ReflectionClass(ApiV1::class);
        $method = $class->getMethod('getDeserializedData');
        $method->setAccessible(true);

        $result = $method->invoke($this->stubApiV1, json_encode($events), $this->serializer);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('events', $result);
        $this->assertCount($size, $result['events']);

        foreach ($result['events'] as $value) {
            $this->assertInstanceOf(GameBufferDTO::class, $value);
        }

        $method = $class->getMethod('getValidatedDTO');
        $method->setAccessible(true);

        $result = $method->invoke($this->stubApiV1, $result['events'], $this->validator);
        $this->assertCount($size, $result);
    }

    /**
     * @dataProvider additionInvalidDTOProvider
     *
     * @param int $size
     * @param array $events
     */
    public function testCheckInvalidData(int $size, array $events)
    {
        $class = new ReflectionClass(ApiV1::class);
        $method = $class->getMethod('getDeserializedData');
        $method->setAccessible(true);

        $result = $method->invoke($this->stubApiV1, json_encode($events), $this->serializer);

        $this->assertIsArray($result);
        $this->assertCount($size, $result);
        if ($size > 0) {
            $this->assertArrayHasKey('events', $result);

            foreach ($result['events'] as $value) {
                $this->assertInstanceOf(GameBufferDTO::class, $value);
            }
    
            $method = $class->getMethod('getValidatedDTO');
            $method->setAccessible(true);

            $result = $method->invoke($this->stubApiV1, $result['events'], $this->validator);
            $this->assertCount(0, $result);
        }
    }

    /**
     * @dataProvider additionFilterProvider
     *
     * @param int $size
     * @param array $parameters
     */
    public function testGetFilterFromRequest(int $size, array $parameters)
    {
        $class = new ReflectionClass(ApiV1::class);
        $method = $class->getMethod('getFilterFromRequest');
        $method->setAccessible(true);

        $result = $method->invoke($this->stubApiV1, new Request($parameters));
        $this->assertCount($size, $result);
    }

    public function additionValidDTOProvider()
    {
        yield [
            1, [
                'events' => [
                    [
                        "lang" => "русский",
                        "sport" => "Баскетбол",
                        "league" => "Евролига",
                        "team1" => "ЦСКА",
                        "team2" => "Панатинаикос",
                        "date" => "2020-02-01 10:00:00",
                        "source" => "sportdata.com"
                    ]
                ]
            ]
        ];
        yield [
            2, [
                'events' => [
                    [
                        "lang" => "русский",
                        "sport" => "Баскетбол",
                        "league" => "Евролига",
                        "team1" => "ЦСКА",
                        "team2" => "Панатинаикос",
                        "date" => "2020-02-01 10:00:00",
                        "source" => "sportdata.com"
                    ],
                    [
                        "lang" => "русский",
                        "sport" => "Хоккей",
                        "league" => "КХЛ",
                        "team1" => "СКА",
                        "team2" => "Сибирь",
                        "date" => "2020-06-13 18:00:00",
                        "source" => "sportdata3.com"
                    ]
                ]
            ]
        ];
    }

    public function additionInvalidDTOProvider()
    {
        yield [
            0, []
        ];
        yield [
            0, [
                'events' => [
                    [
                        "lang" => "русский",
                        "sport" => "Баскетбол",
                        "league" => "Евролига",
                        "team1" => "ЦСКА",
                        "team2" => "Панатинаикос",
                        "date" => "test",
                        "source" => "sportdata.com"
                    ]
                ]
            ]
        ];
        yield [
            0, [
                'events' => [
                    [
                        "lang" => "русский",
                        "sport" => "Баскетбол",
                        "league" => "Евролига",
                        "team1" => "ЦСКА",
                        "team2" => "Панатинаикос",
                        "date" => "",
                        "source" => "sportdata.com"
                    ]
                ]
            ]
        ];
        yield [
            1, [
                'events' => [
                    [
                        "lang" => "русский",
                        "sport" => "Баскетбол",
                        "league" => "Евролига",
                        "team1" => "ЦСКА",
                        "team2" => "Панатинаикос",
                        "date" => null,
                        "source" => "sportdata.com"
                    ]
                ]
            ]
        ];
        yield [
            1, [
                'events' => [
                    [
                        "lang" => "русский",
                        "sport" => "Баскетбол",
                        "league" => "Евролига",
                        "team1" => "ЦСКА",
                        "team2" => "",
                        "date" => "2020-02-01 10:00:00",
                        "source" => "sportdata.com"
                    ]
                ]
            ]
        ];
        yield [
            1, [
                'events' => [
                    [
                        "lang" => "",
                        "sport" => null,
                        "league" => "",
                        "team1" => null,
                        "team2" => "",
                        "date" => "2020-02-01 10:00:00",
                        "source" => "sportdata.com"
                    ]
                ]
            ]
        ];
        yield [
            1, [
                'events' => [
                    [
                        "lang" => "русский",
                        "sport" => "Баскетбол",
                        "league" => "Евролига",
                        "team1" => "ЦСКА",
                        "team2" => "ЦСКА",
                        "date" => "2020-02-01 10:00:00",
                        "source" => "sportdata.com"
                    ]
                ]
            ]
        ];
    }

    public function additionFilterProvider()
    {
        yield [
            0, []
        ];
        yield [
            1, ['source' => 'sportdata1.com']
        ];
        yield [
            0, ['source' => ' ']
        ];
        yield [
            0, ['source' => null]
        ];
        yield [
            2, [
                'start' => '2020-01-00 10:00:00',
                'end' => '2020-10-01 10:00:00'
            ]
        ];
        yield [
            3, [
                'source' => 'sportdata1.com',
                'start' => '2020-01-00 10:00:00',
                'end' => '2020-10-01 10:00:00'
            ]
        ];
        yield [
            1, [
                'source' => 'sportdata1.com',
                'start' => 'test',
                'end' => '2020-10-01 10:00:00'
            ]
        ];
        yield [
            1, [
                'source' => 'sportdata1.com',
                'start' => '2020-01-00 10:00:00',
                'end' => 'test'
            ]
        ];
        yield [
            1, [
                'source' => 'sportdata1.com',
                'start' => 'test',
                'end' => 'test'
            ]
        ];
        yield [
            0, [
                'start' => null,
                'end' => 'test'
            ]
        ];
        yield [
            0, [
                'end' => 'test'
            ]
        ];
        yield [
            0, [
                'end' => '2020-01-00 10:00:00'
            ]
        ];
    }
}