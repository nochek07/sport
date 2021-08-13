<?php

namespace App\Tests\Command;

use App\Command\AddSportCommand;
use App\DTO\GameBufferDTO;
use App\Entity\{Game, GameBuffer};
use App\Service\PropertyBuilder;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class AddSportCommandTest extends KernelTestCase
{
    /**
     * @var integer
     */
    private $id;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = self::$container;
        $propertyBuilder = $container->get(PropertyBuilder::class);

        $event = [
            "lang" => "русский",
            "sport" => "Баскетбол",
            "league" => "Суперлига 1",
            "team1" => "Урал",
            "team2" => "Автодор",
            "date" => "2020-03-01 11:00:00",
            "source" => "sportdata1.com"
        ];
        $dto = new GameBufferDTO($event);
        $propertyBuilder->fillingData([$dto]);
        $filter = $propertyBuilder->getFilterData($dto);

        $gameBufferTest = new GameBuffer();
        $gameBufferTest->setLeague($filter['league']);
        $gameBufferTest->setLanguage($filter['language']);
        $gameBufferTest->setTeam1($filter['team1']);
        $gameBufferTest->setTeam2($filter['team2']);
        $gameBufferTest->setDate($filter['date']);
        $gameBufferTest->setSource($filter['source']);

        $manager = $container->get('doctrine.orm.entity_manager');
        $manager->persist($gameBufferTest);
        $manager->flush();

        $this->id = $gameBufferTest->getId();
    }

    public function testAddSport()
    {
        $this->assertGreaterThan(0, $this->id);

        $this->executeCommand([
            'serialisedData' => json_encode([$this->id, 4])
        ]);

        $container = self::$container;
        /**
         * @var GameBuffer $gameBuffer
         */
        $gameBuffer = $container->get('doctrine.orm.entity_manager')
            ->getRepository(GameBuffer::class)
            ->find($this->id);
        $this->assertNotNull($gameBuffer);
        
        $game = $gameBuffer->getGame();
        $this->assertNotNull($game);
        $this->assertInstanceOf(Game::class, $game);
    }

    /**
     * @param array $arguments
     * @param array $inputs
     */
    private function executeCommand(array $arguments, array $inputs = [])
    {
        /**
         * @var Command $command
         */
        $command = self::$container->get(AddSportCommand::class);
        $command->setApplication(new Application(self::$kernel));

        $commandTester = new CommandTester($command);
        $commandTester->setInputs($inputs);
        $commandTester->execute($arguments);
    }
}