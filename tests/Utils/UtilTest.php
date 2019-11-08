<?php

namespace App\Tests\Utils;

use App\Utils\Util;
use PHPUnit\Framework\TestCase;

class UtilTest extends TestCase
{
    public function testArrayToString()
    {
        $this->assertSame('(1,2),(3,4)', Util::arrayToString([[1,2],[3,4]]));
    }
}