<?php

namespace App\Utils\Property;

use App\DTO\GameBufferDTO;

abstract class AbstractTeam extends AbstractProperty
{
    /**
     * Find by criteria
     *
     * @param array $criteria
     *
     * @return \App\Entity\Team[]
     */
    public function findBy(array $criteria)
    {
        return $this->getManager()
                ->getRepository(\App\Entity\Team::class)
                ->findByPair($criteria) ?? [];
    }

    /**
     * Is equal
     *
     * @param \App\Entity\Team $entity
     * @param GameBufferDTO $dto
     * @param \App\Entity\Sport|null $sport
     *
     * @return bool
     */
    public function isEq($entity, GameBufferDTO $dto, $sport = null): bool
    {
        $value = $this->getValueOfTeam($dto);
        return (strcasecmp($entity->getName(), $value) == 0
            && $entity->getSport() == $sport);
    }

    /**
     * Insert Entity
     *
     * @param GameBufferDTO $dto
     * @param \App\Entity\Sport|null $sport
     *
     * @return \App\Entity\Team
     */
    public function insert(GameBufferDTO $dto, $sport = null)
    {
        $value = $this->getValueOfTeam($dto);
        $team = new \App\Entity\Team();
        $team->setName($value);
        $team->setSport($sport);
        $this->getManager()->persist($team);
        return $team;
    }

    /**
     * Get default value of Team
     *
     * @param GameBufferDTO $dto
     *
     * @return string
     */
    abstract public function getValueOfTeam(GameBufferDTO $dto): string;
}