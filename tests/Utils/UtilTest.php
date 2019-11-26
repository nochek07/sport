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