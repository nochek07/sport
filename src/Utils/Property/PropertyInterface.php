<?php

namespace App\Utils\Property;

use App\DTO\GameBufferDTO;

interface PropertyInterface
{
    /**
     * Find by criteria
     *
     * @param array $criteria
     *
     * @return mixed
     */
    public function findBy(array $criteria);

    /**
     * Is equal
     *
     * @param mixed $entity
     * @param GameBufferDTO $dto
     * @param \App\Entity\Sport|null $sport
     * @return bool
     */
    public function isEq($entity, GameBufferDTO $dto, $sport = null): bool;

    /**
     * Insert Entity
     *
     * @param GameBufferDTO $dto
     * @param \App\Entity\Sport|null $sport
     *
     * @return mixed
     */
    public function insert(GameBufferDTO $dto, $sport = null);
}