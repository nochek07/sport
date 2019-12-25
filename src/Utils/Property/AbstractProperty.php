<?php

namespace App\Utils\Property;

use App\DTO\GameBufferDTO;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractProperty implements PropertyInterface, OutDataInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var array
     */
    private $outData = [];

    /**
     * @var array
     */
    private $inData = [];

    /**
     * PropSport constructor.
     *
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Get Entity Manager
     *
     * @return EntityManagerInterface
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * Get Out Data
     *
     * @return array
     */
    public function getOutData()
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
        array_push($this->inData, $value);
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
     * @param \App\Entity\Sport|null $sport
     *
     * @return mixed|null
     */
    public function lookForOutData(GameBufferDTO $dto, $sport = null)
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
    abstract public function isEq($entity, GameBufferDTO $dto, $sport = null): bool;

    /**
     * {@inheritDoc}
     */
    abstract public function insert(GameBufferDTO $dto, $sport = null);
}