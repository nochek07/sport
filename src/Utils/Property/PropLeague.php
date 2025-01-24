<?php

namespace App\Utils\Property;

use App\DTO\GameBufferDTO;
use App\Repository\LeagueRepository;
use App\Entity\{League, Sport};

class PropLeague extends AbstractProperty
{
    /**
     * Find by criteria
     *
     * @param array $criteria
     * @return League[]
     */
    public function findBy(array $criteria): array
    {
        /**
         * @var LeagueRepository $leagueRepository
         */
        $leagueRepository = $this->getManager()
            ->getRepository(League::class);
        return $leagueRepository->findByPair($criteria) ?? [];
    }

    /**
     * Is equal
     *
     * @param League $entity
     * @param GameBufferDTO $dto
     * @param Sport|null $sport
     * @return bool
     */
    public function isEq($entity, GameBufferDTO $dto, ?Sport $sport = null): bool
    {
        return (strcasecmp($entity->getName(), $dto->getLeague()) == 0
            && !is_null($sport) && $entity->getSport() === $sport);
    }

    /**
     * Insert Entity
     */
    public function insert(GameBufferDTO $dto, ?Sport $sport = null): League
    {
        $league = new League();
        $league->setName($dto->getLeague());
        $league->setSport($sport);
        $this->getManager()->persist($league);
        return $league;
    }
}