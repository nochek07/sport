<?php

namespace App\Utils\Property;

use App\DTO\GameBufferDTO;
use App\Repository\TeamRepository;
use App\Entity\{Sport, Team};

abstract class AbstractTeam extends AbstractProperty
{
    /**
     * Find by criteria
     *
     * @param array $criteria
     * @return Team[]
     */
    public function findBy(array $criteria): array
    {
        /**
         * @var TeamRepository $teamRepository
         */
        $teamRepository = $this->getManager()
            ->getRepository(Team::class);
        return $teamRepository->findByPair($criteria) ?? [];
    }

    /**
     * Is equal
     *
     * @param Team $entity
     * @param GameBufferDTO $dto
     * @param Sport|null $sport
     *
     * @return bool
     */
    public function isEq($entity, GameBufferDTO $dto, ?Sport $sport = null): bool
    {
        $value = $this->getValueOfTeam($dto);
        return (strcasecmp($entity->getName(), $value) == 0
            && $entity->getSport() === $sport);
    }

    /**
     * Insert Entity
     */
    public function insert(GameBufferDTO $dto, ?Sport $sport = null): Team
    {
        $value = $this->getValueOfTeam($dto);
        $team = new Team();
        $team->setName($value);
        $team->setSport($sport);
        $this->getManager()->persist($team);
        return $team;
    }

    /**
     * Get default value of Team
     */
    abstract public function getValueOfTeam(GameBufferDTO $dto): string;
}