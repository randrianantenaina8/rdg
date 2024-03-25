<?php

namespace App\Tests;

use App\Entity\ReuseType;
use PHPUnit\Framework\TestCase;

/**
 * ReuseTypeUnitTest Entity Unit Tests
 *
 * @group Unit
 *
 * @author Roger RAKOTOFIRINGA
 */
class ReuseTypeUnitTest extends TestCase
{
    public function testIsTrue(): void
    {
        $reuseType = new ReuseType();
        $reuseType->setName('name');
        $this->assertEquals('name', $reuseType->getName());
        $this->assertIsString($reuseType->getName());
    }
}
