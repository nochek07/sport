<?php

namespace App\Utils\Property;

use App\DTO\GameBufferDTO;

class Sport extends AbstractProperty
{
    /**
     * Find by criteria
     *
     * @param array $criteria
     *
     * @return \App\Entity\Sport[]
     */
    public function findBy(array $criteria)
    {
        return $this->getManager()
                ->getRepository(\App\Entity\Sport::class)
                ->findBy(['name' => $criteria]) ?? [];
    }

    /**
     * Is equal
     *
     * @param \App\Entity\Sport $entity
     * @param GameBufferDTO $dto
     * @param \App\Entity\Sport $sport
     *
     * @return bool
     */
    public function isEq($entity, GameBufferDTO $dto, $sport = null): bool
    {
        return (strcasecmp($entity->getName(), $dto->getSport()) == 0);
    }

    /**
     * Insert Entity
     *
     * @param GameBufferDTO $dto
     * @param \App\Entity\Sport|null $sport
     *
     * @return \App\Entity\Sport
     */
    public function insert(GameBufferDTO $dto, $sport = null)
    {
        $sportEntity = new \App\Entity\Sport();
        $sportEntity->setName($dto->getSport());
        $this->getManager()->persist($sportEntity);
        return $sportEntity;
    }
}