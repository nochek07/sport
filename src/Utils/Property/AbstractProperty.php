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
     * Sport constructor.
     *
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function getManager()
    {
        return $this->manager;
    }

    public function getOutData()
    {
        return $this->outData;
    }
    public function addOutData(array $data)
    {
        $this->outData = array_merge($this->outData, $data);
    }

    /**
     * @param array|string $data
     */
    public function addInData($data)
    {
        array_push($this->inData, $data);
    }

    public function filingOutData()
    {
        $this->outData = $this->findBy($this->inData);
    }

    /**
     * Look for OutData
     *
     * @param GameBufferDTO $dto
     * @param \App\Entity\Sport|null $sport
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

    abstract public function findBy(array $criteria);

    abstract public function isEq($entity, GameBufferDTO $dto, $sport = null): bool;

    abstract public function insert(GameBufferDTO $dto, $sport = null);
}