<?php

namespace App\Utils\Property;

use Doctrine\ORM\EntityManagerInterface;

class Language implements PropertyInterface
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
     * @return \App\Entity\Language[]
     */
    public function findBy(array $criteria)
    {
        return $this->manager
                ->getRepository(\App\Entity\Language::class)
                ->findBy(['name' => $criteria]) ?? [];
    }

    /**
     * Is equal
     *
     * @param \App\Entity\Language $entity
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
     * @return \App\Entity\Language
     */
    public function insert(...$params)
    {
        $lang = new \App\Entity\Language();
        $lang->setName($params[0]);
        $this->manager->persist($lang);
        return $lang;
    }
}