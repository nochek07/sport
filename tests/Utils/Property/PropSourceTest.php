<?php

namespace App\Tests\Utils\Property;

use App\Utils\Property\PropSource;
use App\DTO\GameBufferDTO;
use App\Entity\Source;
use Doctrine\ORM\EntityManagerInterface;

class PropSourceTest extends TestingPropWithoutSport
{
    public function setUp(): void
    {
        $manager = $this->createMock(EntityManagerInterface::class);
        $this->prop = new PropSource($manager);
    }

    public function getStubDTO(string $value)
    {
        $stubDTO = $this->createMock(GameBufferDTO::class);
        $stubDTO->method('getSource')
            ->willReturn($value);;
        return $stubDTO;
    }

    public function getClassName(): string
    {
        return Source::class;
    }
}