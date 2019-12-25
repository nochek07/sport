<?php

namespace App\Tests\Utils\Property;

use App\Utils\Property\{AbstractTeam, PropTeam2};
use App\DTO\GameBufferDTO;
use App\Entity\Team;
use Doctrine\ORM\EntityManagerInterface;

class PropTeam2Test extends TestingPropWithSport
{
    public function setUp(): void
    {
        $manager = $this->createMock(EntityManagerInterface::class);
        $this->prop = new PropTeam2($manager);
    }

    public function testInstanceOf()
    {
        parent::testInstanceOf();
        $this->assertInstanceOf(AbstractTeam::class, $this->prop);
    }

    public function getStubDTO(string $value)
    {
        $stubDTO = $this->createMock(GameBufferDTO::class);
        $stubDTO->method('getTeam2')
            ->willReturn($value);
        return $stubDTO;
    }

    public function getClassName(): string
    {
        return Team::class;
    }
}