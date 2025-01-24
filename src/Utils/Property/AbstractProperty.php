<?php

namespace App\Utils\Property;

use App\DTO\GameBufferDTO;
use App\Entity\Sport;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractProperty implements PropertyInterface, OutDataInterface
{
    private EntityManagerInterface $manager;

    private array $outData = [];

    private array $inData = [];

    /**
     * PropSport constructor.
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Get Entity Manager
     */
    public function getManager(): EntityManagerInterface
    {
        return $this->manager;
    }

    /**
     * Get Out Data
     */
    public function getOutData(): array
    {
        return $this->outData;
    }

    /**
     * {@inheritDoc}
     */
    public function addOutData(array $data)
    {
        $this->outData = array_merge($this->outData, $data);
    }

    /**
     * {@inheritDoc}
     */
    public function addInData($value) {
        $this->inData[] = $value;
    }

    /**
     * Filling Out Data
     */
    public function filingOutData()
    {
        $this->outData = $this->findBy($this->inData);
    }

    /**
     * Look for OutData
     *
     * @param GameBufferDTO $dto
     * @param Sport|null $sport
     * @return mixed|null
     */
    public function lookForOutData(GameBufferDTO $dto, ?Sport $sport = null)
    {
        foreach ($this->outData as $entity) {
            if ($this->isEq($entity, $dto, $sport)) {
                return $entity;
            }
        }
        return null;
    }

    /**
     * {@inheritDoc}
     */
    abstract public function findBy(array $criteria);

    /**
     * {@inheritDoc}
     */
    abstract public function isEq($entity, GameBufferDTO $dto, ?Sport $sport = null): bool;

    /**
     * {@inheritDoc}
     */
    abstract public function insert(GameBufferDTO $dto, ?Sport $sport = null);
}