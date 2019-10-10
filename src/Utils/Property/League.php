<?php

namespace App\Utils\Property;

use Doctrine\ORM\EntityManagerInterface;

class League implements PropertyInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * Team constructor.
     *
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Find by criteria
     *
     * @param array $criteria
     *
     * @return \App\Entity\Team[]
     */
    public function findBy(array $criteria)
    {
        return $this->manager
                ->getRepository(\App\Entity\League::class)
                ->findByPair($criteria) ?? [];
    }

    /**
     * Is equal
     *
     * @param \App\Entity\League $entity
     * @param mixed ...$params
     *
     * @return bool
     */
    public function isEq($entity, ...$params): bool
    {
        return (strcasecmp($entity->getName(), trim($params[0])) == 0
            && $entity->getSport() == $params[1]);
    }

    /**
     * Insert Entity
     *
     * @param mixed ...$params
     *
     * @return \App\Entity\League
     */
    public function insert(...$params)
    {
        $league = new \App\Entity\League();
        $league->setName($params[0]);
        $league->setSport($params[1]);
        $this->manager->persist($league);
        return $league;
    }
}