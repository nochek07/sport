<?php

namespace App\Utils\Property;

use App\DTO\GameBufferDTO;
use App\Entity\Sport;

class PropSport extends AbstractProperty
{
    /**
     * Find by criteria
     *
     * @param array $criteria
     * @return Sport[]
     */
    public function findBy(array $criteria): array
    {
        return $this->getManager()
            ->getRepository(Sport::class)
            ->findBy(['name' => $criteria]) ?? [];
    }

    /**
     * Is equal
     *
     * @param Sport $entity
     * @param GameBufferDTO $dto
     * @param Sport|null $sport
     * @return bool
     */
    public function isEq($entity, GameBufferDTO $dto, ?Sport $sport = null): bool
    {
        return (strcasecmp($entity->getName(), $dto->getSport()) == 0);
    }

    /**
     * Insert Entity
     */
    public function insert(GameBufferDTO $dto, ?Sport $sport = null): Sport
    {
        $sportEntity = new Sport();
        $sportEntity->setName($dto->getSport());
        $this->getManager()->persist($sportEntity);
        return $sportEntity;
    }
}