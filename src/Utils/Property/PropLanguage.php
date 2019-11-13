<?php

namespace App\Utils\Property;

use App\DTO\GameBufferDTO;

class PropLanguage extends AbstractProperty
{
    /**
     * Find by criteria
     *
     * @param array $criteria
     *
     * @return \App\Entity\Language[]
     */
    public function findBy(array $criteria)
    {
        return $this->getManager()
                ->getRepository(\App\Entity\Language::class)
                ->findBy(['name' => $criteria]) ?? [];
    }

    /**
     * Is equal
     *
     * @param \App\Entity\Language $entity
     * @param GameBufferDTO $dto
     * @param \App\Entity\Sport|null $sport
     *
     * @return bool
     */
    public function isEq($entity, GameBufferDTO $dto, $sport = null): bool
    {
        return (strcasecmp($entity->getName(), $dto->getLanguage()) == 0);
    }

    /**
     * Insert Entity
     *
     * @param GameBufferDTO $dto
     * @param \App\Entity\Sport|null $sport
     *
     * @return \App\Entity\Language
     */
    public function insert(GameBufferDTO $dto, $sport = null)
    {
        $lang = new \App\Entity\Language();
        $lang->setName($dto->getLanguage());
        $this->getManager()->persist($lang);
        return $lang;
    }
}