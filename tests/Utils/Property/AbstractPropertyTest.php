<?php

namespace App\Tests\Utils\Property;

use App\DTO\GameBufferDTO;
use App\Utils\Property\{AbstractProperty, OutDataInterface, PropertyInterface};
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class AbstractPropertyTest extends TestCase
{
    private AbstractProperty $stubAbstractProperty;

    public function setUp(): void
    {
        $this->stubAbstractProperty = $this->getMockForAbstractClass(AbstractProperty::class, [], '', false);
    }

    public function testInstanceOf(): void
    {
        $this->assertInstanceOf(PropertyInterface::class, $this->stubAbstractProperty);
        $this->assertInstanceOf(OutDataInterface::class, $this->stubAbstractProperty);
    }

    public function testAddOutData(): void
    {
        $this->assertCount(0, $this->stubAbstractProperty->getOutData());

        $this->stubAbstractProperty->addOutData([1]);
        $this->assertCount(1, $this->stubAbstractProperty->getOutData());

        $this->stubAbstractProperty->addOutData([1,2]);
        $this->assertCount(3, $this->stubAbstractProperty->getOutData());

        $this->stubAbstractProperty->addOutData([]);
        $this->assertCount(3, $this->stubAbstractProperty->getOutData());
    }

    /**
     * @throws \ReflectionException
     */
    public function testAddInData(): void
    {
        $this->assertCount(0, self::getPrivate($this->stubAbstractProperty, 'inData'));

        $this->stubAbstractProperty->addInData(1);
        $this->assertCount(1, self::getPrivate($this->stubAbstractProperty, 'inData'));

        $this->stubAbstractProperty->addInData('2');
        $this->assertCount(2, self::getPrivate($this->stubAbstractProperty, 'inData'));
    }

    public function testFilingOutData(): void
    {
        $this->stubAbstractProperty->expects($this->any())
            ->method('findBy')
            ->willReturn([], [1]);

        $this->stubAbstractProperty->filingOutData();
        $this->assertCount(0, $this->stubAbstractProperty->getOutData());

        $this->stubAbstractProperty->filingOutData();
        $this->assertCount(1, $this->stubAbstractProperty->getOutData());
    }

    public function testLookForOutData(): void
    {
        $stubDTO = $this->createMock(GameBufferDTO::class);
        $this->assertNull($this->stubAbstractProperty->lookForOutData($stubDTO));
    }

    /**
     * @throws \ReflectionException
     */
    public static function getPrivate($object, $property)
    {
        $reflector = new ReflectionProperty(AbstractProperty::class, $property);
        $reflector->setAccessible(true);
        return $reflector->getValue($object);
    }
}