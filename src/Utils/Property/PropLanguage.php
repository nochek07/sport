<?php

namespace App\Utils\Property;

use App\DTO\GameBufferDTO;
use App\Entity\{Language, Sport};

class PropLanguage extends AbstractProperty
{
    /**
     * Find by criteria
     *
     * @param array $criteria
     * @return Language[]
     */
    public function findBy(array $criteria): array
    {
        return $this->getManager()
            ->getRepository(Language::class)
            ->findBy(['name' => $criteria]) ?? [];
    }

    /**
     * Is equal
     *
     * @param Language $entity
     * @param GameBufferDTO $dto
     * @param Sport|null $sport
     * @return bool
     */
    public function isEq($entity, GameBufferDTO $dto, ?Sport $sport = null): bool
    {
        return (strcasecmp($entity->getName(), $dto->getLanguage()) == 0);
    }

    /**
     * Insert Entity
     */
    public function insert(GameBufferDTO $dto, ?Sport $sport = null): Language
    {
        $lang = new Language();
        $lang->setName($dto->getLanguage());
        $this->getManager()->persist($lang);
        return $lang;
    }
}