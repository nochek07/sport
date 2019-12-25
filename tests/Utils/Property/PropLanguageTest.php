<?php

namespace App\Tests\Utils\Property;

use App\Utils\Property\PropLanguage;
use App\DTO\GameBufferDTO;
use App\Entity\Language;
use Doctrine\ORM\EntityManagerInterface;

class PropLanguageTest extends TestingPropWithoutSport
{
    public function setUp(): void
    {
        $manager = $this->createMock(EntityManagerInterface::class);
        $this->prop = new PropLanguage($manager);
    }

    public function getStubDTO(string $value)
    {
        $stubDTO = $this->createMock(GameBufferDTO::class);
        $stubDTO->method('getLanguage')
            ->willReturn($value);;
        return $stubDTO;
    }

    public function getClassName(): string
    {
        return Language::class;
    }
}