<?php

namespace App\Tests\Utils;

use App\Utils\Util;
use PHPUnit\Framework\TestCase;

class UtilTest extends TestCase
{
    /**
     * @dataProvider additionProvider
     *
     * @param string $expected
     * @param array $data
     */
    public function testArrayToString($expected, $data)
    {
        $this->assertSame($expected, Util::arrayToString($data));
    }

    public function testIsDate()
    {
        $this->assertFalse(Util::isDate(''));
        $this->assertFalse(Util::isDate(' '));
        $this->assertFalse(Util::isDate(' test '));

        $this->assertTrue(Util::isDate('2020-02-01 10:00:00'));
        $this->assertTrue(Util::isDate('2020-02-01'));
    }

    public function additionProvider()
    {
        return [
            ["(1,2),(0,4)", [[1,2],[0,4]]],
            ["('1',2),(3,4)", [['1',2],[3,4]]],
            ["('1',2),(3,'4')", [['1',2],[3,'4']]],
            ["('1','2'),('3','4')", [['1','2'],['3','4']]],
            ["1,2,0,4", [1,2,0,4]],
            ["'1',2,3,4", ['1',2,3,4]],
            ["'1','2','3','4'", ['1','2','3','4']],
            ['', []]
        ];
    }
}