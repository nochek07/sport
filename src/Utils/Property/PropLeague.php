<?php

namespace App\Utils\Property;

use App\DTO\GameBufferDTO;

class PropLeague extends AbstractProperty
{
    /**
     * Find by criteria
     *
     * @param array $criteria
     *
     * @return \App\Entity\League[]
     */
    public function findBy(array $criteria)
    {
        return $this->getManager()
                ->getRepository(\App\Entity\League::class)
                ->findByPair($criteria) ?? [];
    }

    /**
     * Is equal
     *
     * @param \App\Entity\League $entity
     * @param GameBufferDTO $dto
     * @param \App\Entity\Sport $sport
     *
     * @return bool
     */
    public function isEq($entity, GameBufferDTO $dto, $sport = null): bool
    {
        return (strcasecmp($entity->getName(), $dto->getLeague()) == 0
            && !is_null($sport) && $entity->getSport() == $sport);
    }

    /**
     * Insert Entity
     *
     * @param GameBufferDTO $dto
     * @param \App\Entity\Sport|null $sport
     *
     * @return \App\Entity\League
     */
    public function insert(GameBufferDTO $dto, $sport = null)
    {
        $league = new \App\Entity\League();
        $league->setName($dto->getLeague());
        $league->setSport($sport);
        $this->getManager()->persist($league);
        return $league;
    }
}