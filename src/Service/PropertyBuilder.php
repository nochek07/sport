<?php

namespace App\Service;

use App\Utils\Property\PropertyInterface;
use Doctrine\ORM\EntityManagerInterface;

class PropertyBuilder
{
    const LANGUAGE = 'Language';
    const SPORT = 'Sport';
    const LEAGUE = 'League';
    const TEAM = 'Team';
    const SOURCE = 'Source';

    /**
     * @var array
     */
    private $dataEvents = [
        self::LANGUAGE => ['in' => [], 'out' => []],
        self::SPORT => ['in' => [], 'out' => []],
        self::LEAGUE => ['in' => [], 'out' => []],
        self::TEAM => ['in' => [], 'out' => []],
        self::SOURCE => ['in' => [], 'out' => []],
    ];

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * PropertyBuilder constructor.
     *
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Add in data (in)
     *
     * @param string $key - const PropertyBuilder
     * @param mixed $value
     */
    public function addDataIn(string $key, $value)
    {
        $this->dataEvents[$key]['in'][] = $value;
    }

    /**
     * Add in data (out)
     *
     * @param string $key - const PropertyBuilder
     * @param mixed $entity
     */
    public function addDataOut(string $key, $entity)
    {
        $this->dataEvents[$key]['out'][] = $entity;
    }

    public function fillingData()
    {
        foreach ($this->dataEvents as $key => &$value) {
            $property = $this->create($key);
            $value['prop'] = $property;
            $value['out'] = $property->findBy($value['in']);
        }
    }

    /**
     * Create Property
     *
     * @param string $name
     *
     * @return PropertyInterface|null
     */
    public function create(string $name)
    {
        $newName = "App\\Utils\\Property\\" . $name;
        try {
            return new $newName($this->manager);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Look for Data
     *
     * @param string $key - const PropertyBuilder
     * @param mixed ...$params
     *
     * @return mixed|null
     */
    public function lookForData(string $key, ...$params)
    {
        /**
         * @var PropertyInterface $property
         */
        $property = $this->dataEvents[$key]['prop'];
        foreach ($this->dataEvents[$key]['out'] as $entity) {
            if ($property->isEq($entity, ...$params)) {
                return $entity;
            }
        }
        return null;
    }

    /**
     * Insert Entity
     *
     * @param string $key - const PropertyBuilder
     * @param mixed ...$params
     *
     * @return mixed
     */
    public function insertEntity(string $key, ...$params)
    {
        /**
         * @var PropertyInterface $property
         */
        $property = $this->dataEvents[$key]['prop'];
        $entity = $property->insert(...$params);
        $this->addDataOut($key, $entity);
        return $entity;
    }
}