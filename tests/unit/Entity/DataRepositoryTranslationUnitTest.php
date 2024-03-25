<?php

namespace App\Tests;

use App\Entity\DataRepositoryTranslation;
use PHPUnit\Framework\TestCase;

/**
 * DataRepositoryTranslationUnitTest Entity Unit Tests
 *
 * @group Unit
 *
 * @author Roger RAKOTOFIRINGA
 */
final class DataRepositoryTranslationUnitTest extends TestCase
{
    public function testIsTrue(): void
    {
        $dataRepositoryTranslation = new DataRepositoryTranslation();
        $dataRepositoryTranslation->setName('nom1')
                                  ->setDescription('description1')
                                  ->setUrl('url')
                                  ->setDataType('data type')
                                  ->setRepositoryModeration('repository moderation')
                                  ->setEmbargo('embargo1');

        $this->assertEquals('nom1', $dataRepositoryTranslation->getName());
        $this->assertEquals('description1', $dataRepositoryTranslation->getDescription());
        $this->assertEquals('url', $dataRepositoryTranslation->getUrl());
        $this->assertEquals('data type', $dataRepositoryTranslation->getDataType());
        $this->assertEquals('repository moderation', $dataRepositoryTranslation->getRepositoryModeration());
        $this->assertEquals('embargo1', $dataRepositoryTranslation->getEmbargo());

        $this->assertIsString($dataRepositoryTranslation->getName());
        $this->assertIsString($dataRepositoryTranslation->getDescription());
        $this->assertIsString($dataRepositoryTranslation->getUrl());
        $this->assertIsString($dataRepositoryTranslation->getDataType());
        $this->assertIsString($dataRepositoryTranslation->getRepositoryModeration());
        $this->assertIsString($dataRepositoryTranslation->getEmbargo());
    }
}
