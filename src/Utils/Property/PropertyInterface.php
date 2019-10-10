<?php

namespace App\Utils\Property;

interface PropertyInterface
{
    /**
     * Find by criteria
     *
     * @param array $criteria
     *
     * @return mixed
     */
    public function findBy(array $criteria);

    /**
     * Is equal
     *
     * @param mixed $entity
     * @param mixed ...$params
     * @return bool
     */
    public function isEq($entity, ...$params): bool;

    /**
     * Insert Entity
     *
     * @param mixed ...$params
     *
     * @return mixed
     */
    public function insert(...$params);
}