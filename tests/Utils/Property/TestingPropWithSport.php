<?php

namespace App\Tests\Utils\Property;

use App\Entity\Sport;
use App\Utils\Property\{AbstractProperty, OutDataInterface, PropertyInterface};
use PHPUnit\Framework\TestCase;

abstract class TestingPropWithSport extends TestCase
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
        $stubSport1 = $this->getStubSport('test');
        $stubProp = $this->getStubProp($valueEntity, $stubSport1);
        $stubDTO = $this->getStubDTO($valueDTO);

        $this->assertTrue($this->prop->isEq($stubProp, $stubDTO, $stubSport1));

        $stubSport2 = $this->getStubSport('test');
        $this->assertTrue($this->prop->isEq($stubProp, $stubDTO, $stubSport2));
    }

    /**
     * @dataProvider additionProviderForNotEqual
     *
     * @param string $valueEntity
     * @param string $valueDTO
     * @param string $nameSport
     */
    public function testIsNotEqual(string $valueEntity, string $valueDTO, string $nameSport)
    {
        $stubSport1 = $this->getStubSport('test');
        $stubProp = $this->getStubProp($valueEntity, $stubSport1);
        $stubDTO = $this->getStubDTO($valueDTO);

        $this->assertFalse($this->prop->isEq($stubProp, $stubDTO));

        $stubSport2 = $this->getStubSport($nameSport);
        $this->assertFalse($this->prop->isEq($stubProp, $stubDTO, $stubSport2));
    }

    public function testLookForOutData()
    {
        $stubSport = $this->getStubSport('test');

        $stubDTO = $this->getStubDTO('Test');
        $this->assertNull($this->prop->lookForOutData($stubDTO));

        $stubProp = $this->getStubProp('Test', $stubSport);
        $this->prop->addOutData([$stubProp]);
        $this->assertInstanceOf($this->getClassName(), $this->prop->lookForOutData($stubDTO, $stubSport));

        $this->assertNull($this->prop->lookForOutData($stubDTO));
    }

    public function getStubProp(string $value, Sport $stubSport)
    {
        $stubProp = $this->createMock($this->getClassName());
        $stubProp->method('getName')
            ->willReturn($value);
        $stubProp->method('getSport')
            ->willReturn($stubSport);
        return $stubProp;
    }

    public function getStubSport(string $value)
    {
        $stubSport = $this->createMock(Sport::class);
        $stubSport->method('getName')
            ->willReturn($value);
        return $stubSport;
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
            ['Name ', 'name', 'test'],
            [' ', '  ', 'test'],
            ['NAME', 'tt', 'test'],
            ['123', '1235', 'test'],
            ['Name', 'Name', 'Test'],
            ['Name', 'Name', '11'],
        ];
    }

    abstract public function getStubDTO(string $value);

    abstract public function getClassName(): string;
}