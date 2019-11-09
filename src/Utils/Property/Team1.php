<?php

namespace App\Utils\Property;

use App\DTO\GameBufferDTO;

class Team1 extends AbstractTeam
{
    /**
     * {@inheritDoc}
     */
    public function getValueOfTeam(GameBufferDTO $dto): string
    {
        return $dto->getTeam1();
    }
}