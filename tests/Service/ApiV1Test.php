<?php

namespace App\Tests\Service;

use App\DTO\GameBufferDTO;
use App\Entity\{Game, GameBuffer, Sport};
use App\Service\{ApiV1, PropertyBuilder};
use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiV1Test extends KernelTestCase
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var ApiV1
     */
    private $stubApi;

    /**
     * @var ApiV1
     */
    private $api;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var PropertyBuilder
     */
    private $propertyBuilder;

    public function setUp(): void
    {
        self::bootKernel();
        
        $container = self::$container;
        $this->manager = $container->get('doctrine.orm.entity_manager');
        $this->validator = $container->get("validator");
        $this->serializer = $container->get("serializer");
        $this->propertyBuilder = $container->get(PropertyBuilder::class);
        $this->api = $container->get(ApiV1::class);

        $this->stubApi = $this->createMock(ApiV1::class);
    }

    public function testAddGamesByArray()
    {
        $id = $this->setTestData([
            "lang" => "русский",
            "sport" => "Баскетбол",
            "league" => "Суперлига 1",
            "team1" => "Урал",
            "team2" => "Автодор",
            "date" => "2020-03-01 11:00:00",
            "source" => "sportdata1.com"
        ]);
        $this->assertGreaterThan(0, $id);

        $result = $this->api->addGamesByArray([$id, 4]);
        $this->assertEquals(0, $result);

        $gameBuffer = $this->getGameBuffer($id);
        $this->assertNotNull($gameBuffer);

        $game = $gameBuffer->getGame();
        $this->assertNotNull($game);
        $this->assertInstanceOf(Game::class, $game);
    }

    public function testUpdateGamesByArray()
    {
        $nameSport = "Гандбол";
        $data = [
            "lang" => "русский",
            "sport" => $nameSport,
            "league" => "Высшая лига",
            "team1" => "ЦСКА",
            "team2" => "Нева",
            "date" => "2020-03-01 11:00:00",
            "source" => "sportdata1.com"
        ];
        $id = $this->setTestData($data);
        /**
         * @var Sport $sport
         */
        $sport = $this->manager
            ->getRepository(Sport::class)
            ->findOneBy(["name" => $nameSport]);
        $sport->setDiff(1);
        $this->manager->flush();

        $this->assertGreaterThan(0, $id);
        $this->assertInstanceOf(Sport::class, $sport);

        $result = $this->api->addGamesByArray([$id, 4]);
        $this->assertEquals(0, $result);

        $gameBuffer = $this->getGameBuffer($id);
        $this->assertNotNull($gameBuffer);

        $game = $gameBuffer->getGame();
        $this->assertNotNull($game);
        $this->assertInstanceOf(Game::class, $game);

        $data["date"] = "2020-03-01 11:30:00";
        $data["source"] = "sportdata2.com";
        $idNew = $this->setTestData($data);
        $this->assertGreaterThan(0, $idNew);

        $result = $this->api->addGamesByArray([$idNew, 4]);
        $this->assertEquals(0, $result);

        $gameBufferNew = $this->getGameBuffer($idNew);
        $gameNew = $gameBufferNew->getGame();
        $this->assertSame($gameNew, $game);
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

        $result = $method->invoke($this->stubApi, json_encode($events), $this->serializer);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('events', $result);
        $this->assertCount($size, $result['events']);

        foreach ($result['events'] as $value) {
            $this->assertInstanceOf(GameBufferDTO::class, $value);
        }

        $method = $class->getMethod('getValidatedDTO');
        $method->setAccessible(true);

        $result = $method->invoke($this->stubApi, $result['events'], $this->validator);
        $this->assertIsArray($result);
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

        $result = $method->invoke($this->stubApi, json_encode($events), $this->serializer);

        $this->assertIsArray($result);
        $this->assertCount($size, $result);
        if ($size > 0) {
            $this->assertArrayHasKey('events', $result);

            foreach ($result['events'] as $value) {
                $this->assertInstanceOf(GameBufferDTO::class, $value);
            }
    
            $method = $class->getMethod('getValidatedDTO');
            $method->setAccessible(true);

            $result = $method->invoke($this->stubApi, $result['events'], $this->validator);
            $this->assertIsArray($result);
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

        $result = $method->invoke($this->stubApi, $parameters);
        $this->assertIsArray($result);
        $this->assertCount($size, $result);
    }

    /**
     * @param array $event
     * @return int
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function setTestData(array $event): int
    {
        $dto = $this->serializer->deserialize(json_encode($event), 'App\DTO\GameBufferDTO', JsonEncoder::FORMAT);
        $this->propertyBuilder->fillingData([$dto]);
        $filter = $this->propertyBuilder->getDataFilter($dto);

        $gameBufferTest = new GameBuffer();
        $gameBufferTest->setLeague($filter['league']);
        $gameBufferTest->setLanguage($filter['language']);
        $gameBufferTest->setTeam1($filter['team1']);
        $gameBufferTest->setTeam2($filter['team2']);
        $gameBufferTest->setDate($filter['date']);
        $gameBufferTest->setSource($filter['source']);

        $this->manager->persist($gameBufferTest);
        $this->manager->flush();

        return $gameBufferTest->getId();
    }

    /**
     * @param int $id
     * @return GameBuffer|null
     */
    private function getGameBuffer(int $id): ?GameBuffer
    {
        return $this->manager
            ->getRepository(GameBuffer::class)
            ->find($id);
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