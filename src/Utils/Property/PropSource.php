<?php

namespace App\Utils\Property;

use App\DTO\GameBufferDTO;

class PropSource extends AbstractProperty
{
    /**
     * Find by criteria
     *
     * @param array $criteria
     *
     * @return \App\Entity\Source[]
     */
    public function findBy(array $criteria)
    {
        return $this->getManager()
                ->getRepository(\App\Entity\Source::class)
                ->findBy(['name' => $criteria]) ?? [];
    }

    /**
     * Is equal
     *
     * @param \App\Entity\Source $entity
     * @param GameBufferDTO $dto
     * @param \App\Entity\Sport $sport
     *
     * @return bool
     */
    public function isEq($entity, GameBufferDTO $dto, $sport = null): bool
    {
        return (strcasecmp($entity->getName(), $dto->getSource()) == 0);
    }

    /**
     * Insert Entity
     *
     * @param GameBufferDTO $dto
     * @param \App\Entity\Sport|null $sport
     *
     * @return \App\Entity\Source
     */
    public function insert(GameBufferDTO $dto, $sport = null)
    {
        $source = new \App\Entity\Source();
        $source->setName($dto->getSource());
        $this->getManager()->persist($source);
        return $source;
    }
}