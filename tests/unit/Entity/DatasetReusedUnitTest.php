<?php

namespace App\Tests;

use App\Entity\DatasetReused;
use PHPUnit\Framework\TestCase;

/**
 * DatasetReusedUnitTest Entity Unit Tests
 *
 * @group Unit
 *
 * @author Roger RAKOTOFIRINGA
 */
final class DatasetReusedUnitTest extends TestCase
{
    public function testIsTrue(): void
    {
        $datasetReused = new DatasetReused();
        $dateTime = new \DateTime();
        $datasetReused->setPublicationTitle('publicationTitle')
                      ->setDescription('description')
                      ->setAuthor('author')
                      ->setAuthorAffiliation('authorAffiliation')
                      ->setPublicationDate($dateTime)
                      ->setDatasetReusedDoi('datasetReusedDoi')
                      ->setNewDatasetDoi('newDatasetDoi')
                      ->setNewDatasetUrl('newDatasetUrl')
                      ->setImage('image')
                      ->setEnable(1)
        ;

        $this->assertEquals('publicationTitle', $datasetReused->getPublicationTitle());
        $this->assertEquals('description', $datasetReused->getDescription());
        $this->assertEquals('author', $datasetReused->getAuthor());
        $this->assertEquals('authorAffiliation', $datasetReused->getAuthorAffiliation());
        $this->assertEquals($dateTime, $datasetReused->getPublicationDate());
        $this->assertEquals('datasetReusedDoi', $datasetReused->getDatasetReusedDoi());
        $this->assertEquals('newDatasetDoi', $datasetReused->getNewDatasetDoi());
        $this->assertEquals('image', $datasetReused->getImage());
        $this->assertEquals(1, $datasetReused->isEnable());

        $this->assertIsString($datasetReused->getPublicationTitle());
        $this->assertIsString($datasetReused->getDescription());
        $this->assertIsString($datasetReused->getAuthor());
        $this->assertIsString($datasetReused->getAuthorAffiliation());
        $this->assertIsString($datasetReused->getDatasetReusedDoi());
        $this->assertIsString($datasetReused->getNewDatasetDoi());
        $this->assertIsString($datasetReused->getImage());
        $this->assertIsBool($datasetReused->isEnable());

    }
}
