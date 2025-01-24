<?php

namespace App\Tests\Utils\Property;

use App\DTO\GameBufferDTO;
use App\Entity\Team;
use App\Utils\Property\AbstractTeam;
use Doctrine\ORM\EntityManagerInterface;

class PropTeam1 extends AbstractTestingPropWithSport
{
    public function setUp(): void
    {
        $manager = $this->createMock(EntityManagerInterface::class);
        $this->prop = new PropTeam1($manager);
    }

    public function testInstanceOf(): void
    {
        parent::testInstanceOf();
        $this->assertInstanceOf(AbstractTeam::class, $this->prop);
    }

    public function getStubDTO(string $value)
    {
        $stubDTO = $this->createMock(GameBufferDTO::class);
        $stubDTO->method('getTeam1')
            ->willReturn($value);
        return $stubDTO;
    }

    public function getClassName(): string
    {
        return Team::class;
    }
}