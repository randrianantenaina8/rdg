<?php

namespace App\Tests;

use App\Entity\KeywordTranslation;
use PHPUnit\Framework\TestCase;

/**
 * KeywordTranslationUnitTest Entity Unit Tests
 *
 * @group Unit
 *
 * @author Roger RAKOTOFIRINGA
 */
class KeywordTranslationUnitTest extends TestCase
{
    public function testIsTrue(): void
    {
        $KeywordTranslation = new KeywordTranslation();
        $KeywordTranslation->setTerm('term1');

        $this->assertEquals('term1', $KeywordTranslation->getTerm());
        $this->assertTrue(true);
    }
}
