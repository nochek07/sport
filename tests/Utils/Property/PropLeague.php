<?php

namespace App\Tests\Utils\Property;

use App\DTO\GameBufferDTO;
use App\Entity\League;
use App\Utils\Property\PropLeague;
use Doctrine\ORM\EntityManagerInterface;

class PropLeague extends AbstractTestingPropWithSport
{
    public function setUp(): void
    {
        $manager = $this->createMock(EntityManagerInterface::class);
        $this->prop = new PropLeague($manager);
    }

    public function getStubDTO(string $value)
    {
        $stubDTO = $this->createMock(GameBufferDTO::class);
        $stubDTO->method('getLeague')
            ->willReturn($value);
        return $stubDTO;
    }

    public function getClassName(): string
    {
        return League::class;
    }
}