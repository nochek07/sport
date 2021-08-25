<?php

namespace App\Tests\Utils\Property;

use App\DTO\GameBufferDTO;
use App\Entity\Sport;
use App\Utils\Property\PropSport;
use Doctrine\ORM\EntityManagerInterface;

class PropSport extends AbstractTestingPropWithoutSport
{
    public function setUp(): void
    {
        $manager = $this->createMock(EntityManagerInterface::class);
        $this->prop = new PropSport($manager);
    }

    public function getStubDTO(string $value)
    {
        $stubDTO = $this->createMock(GameBufferDTO::class);
        $stubDTO->method('getSport')
            ->willReturn($value);
        return $stubDTO;
    }

    public function getClassName(): string
    {
        return Sport::class;
    }
}