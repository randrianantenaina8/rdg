<?php

namespace App\Tests;

use App\Entity\DisciplineTranslation;
use PHPUnit\Framework\TestCase;

/**
 * DisciplineTranslationUnitTest Entity Unit Tests
 *
 * @group Unit
 *
 * @author Roger RAKOTOFIRINGA
 */
class DisciplineTranslationUnitTest extends TestCase
{
    public function testIsTrue(): void
    {
        $DisciplineTranslation = new DisciplineTranslation();
        $DisciplineTranslation->setTitle('title1');

        $this->assertEquals('title1', $DisciplineTranslation->getTitle());
        $this->assertTrue(true);
    }
}
