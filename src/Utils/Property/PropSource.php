<?php

namespace App\Utils\Property;

use App\DTO\GameBufferDTO;
use App\Entity\{Source, Sport};

class PropSource extends AbstractProperty
{
    /**
     * Find by criteria
     *
     * @param array $criteria
     * @return Source[]
     */
    public function findBy(array $criteria): array
    {
        return $this->getManager()
            ->getRepository(Source::class)
            ->findBy(['name' => $criteria]) ?? [];
    }

    /**
     * Is equal
     *
     * @param Source $entity
     * @param GameBufferDTO $dto
     * @param Sport|null $sport
     * @return bool
     */
    public function isEq($entity, GameBufferDTO $dto, ?Sport $sport = null): bool
    {
        return (strcasecmp($entity->getName(), $dto->getSource()) == 0);
    }

    /**
     * Insert Entity
     */
    public function insert(GameBufferDTO $dto, ?Sport $sport = null): Source
    {
        $source = new Source();
        $source->setName($dto->getSource());
        $this->getManager()->persist($source);
        return $source;
    }
}