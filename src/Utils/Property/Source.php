<?php

namespace App\Utils\Property;

use Doctrine\ORM\EntityManagerInterface;

class Source implements PropertyInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * Sport constructor.
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
     * @return \App\Entity\Source[]
     */
    public function findBy(array $criteria)
    {
        return $this->manager
                ->getRepository(\App\Entity\Source::class)
                ->findBy(['name' => $criteria]) ?? [];
    }

    /**
     * Is equal
     *
     * @param \App\Entity\Source $entity
     * @param mixed ...$params
     *
     * @return bool
     */
    public function isEq($entity, ...$params): bool
    {
        return (strcasecmp($entity->getName(), trim($params[0])) == 0);
    }

    /**
     * Insert Entity
     *
     * @param mixed ...$params
     *
     * @return \App\Entity\Source
     */
    public function insert(...$params)
    {
        $source = new \App\Entity\Source();
        $source->setName($params[0]);
        $this->manager->persist($source);
        return $source;
    }
}