<?php

namespace App\Utils\Property;

use App\DTO\GameBufferDTO;
use App\Entity\Sport;

interface PropertyInterface
{
    /**
     * Find by criteria
     *
     * @param array $criteria
     * @return mixed
     */
    public function findBy(array $criteria);

    /**
     * Is equal
     *
     * @param mixed $entity
     * @param GameBufferDTO $dto
     * @param Sport|null $sport
     * @return bool
     */
    public function isEq($entity, GameBufferDTO $dto, ?Sport $sport = null): bool;

    /**
     * Insert Entity
     *
     * @param GameBufferDTO $dto
     * @param Sport|null $sport
     * @return mixed
     */
    public function insert(GameBufferDTO $dto, ?Sport $sport = null);
}