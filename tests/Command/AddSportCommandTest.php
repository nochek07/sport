<?php

namespace App\Tests\Command;

use App\Command\AddSportCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class AddSportCommandTest extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
    }

    public function testSuccessCommand(): void
    {
        $result = $this->executeCommand([
            'serialisedData' => '[]'
        ]);
        $this->assertEquals(0, $result);
    }

    public function testFailCommand(): void
    {
        $result = $this->executeCommand([
            'serialisedData' => '5[]'
        ]);
        $this->assertEquals(1, $result);
    }

    /**
     * @param array $arguments
     * @param array $inputs
     * @return int
     */
    private function executeCommand(array $arguments, array $inputs = []): int
    {
        /**
         * @var Command $command
         */
        $command = self::$container->get(AddSportCommand::class);
        $command->setApplication(new Application(self::$kernel));

        $commandTester = new CommandTester($command);
        $commandTester->setInputs($inputs);
        return $commandTester->execute($arguments);
    }
}