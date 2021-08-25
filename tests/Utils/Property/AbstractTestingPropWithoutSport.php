<?php

namespace App\Tests\Utils\Property;

use App\Utils\Property\{AbstractProperty, OutDataInterface, PropertyInterface};
use PHPUnit\Framework\TestCase;

abstract class AbstractTestingPropWithoutSport extends TestCase
{
    /**
     * @var AbstractProperty
     */
    public $prop;

    public function testInstanceOf()
    {
        $this->assertInstanceOf(AbstractProperty::class, $this->prop);
        $this->assertInstanceOf(PropertyInterface::class, $this->prop);
        $this->assertInstanceOf(OutDataInterface::class, $this->prop);
    }

    /**
     * @dataProvider additionProviderForEqual
     *
     * @param string $valueEntity
     * @param string $valueDTO
     */
    public function testIsEqual(string $valueEntity, string $valueDTO)
    {
        $stubSport = $this->getStubProp($valueEntity);
        $stubDTO = $this->getStubDTO($valueDTO);

        $this->assertTrue($this->prop->isEq($stubSport, $stubDTO));
    }

    /**
     * @dataProvider additionProviderForNotEqual
     *
     * @param string $valueEntity
     * @param string $valueDTO
     */
    public function testIsNotEqual(string $valueEntity, string $valueDTO)
    {
        $stubSport = $this->getStubProp($valueEntity);
        $stubDTO = $this->getStubDTO($valueDTO);

        $this->assertFalse($this->prop->isEq($stubSport, $stubDTO));
    }

    public function testLookForOutData()
    {
        $stubDTO = $this->getStubDTO('Test');
        $this->assertNull($this->prop->lookForOutData($stubDTO));

        $stubSport = $this->getStubProp('Test');
        $this->prop->addOutData([$stubSport]);
        $this->assertInstanceOf($this->getClassName(), $this->prop->lookForOutData($stubDTO));
    }

    public function getStubProp(string $value)
    {
        $stubProp = $this->createMock($this->getClassName());
        $stubProp->method('getName')
            ->willReturn($value);
        return $stubProp;
    }

    public function additionProviderForEqual()
    {
        return [
            ['Name', 'name'],
            ['name', 'name'],
            ['NAME', 'NAME'],
            ['naMe', 'NaMe'],
            [' ', ' '],
            ['123', '123'],
        ];
    }

    public function additionProviderForNotEqual()
    {
        return [
            ['Name ', 'name'],
            [' ', '  '],
            ['NAME', 'tt'],
            ['123', '1235'],
        ];
    }

    abstract public function getStubDTO(string $value);

    abstract public function getClassName(): string;
}