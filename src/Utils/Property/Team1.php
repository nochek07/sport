<?php

namespace App\Utils\Property;

use App\DTO\GameBufferDTO;

class Team1 extends AbstractTeam
{
    public function getValueOfTeam(GameBufferDTO $dto): string
    {
        return $dto->getTeam1();
    }
}