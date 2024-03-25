<?php

namespace App\Tests;

use App\Entity\DataRepository;
use App\Entity\SupportingInstitutionTranslation;
use App\Entity\DisciplineTranslation;
use App\Entity\KeywordTranslation;
use PHPUnit\Framework\TestCase;

/**
 * DisciplineUnitTest Entity Unit Tests
 *
 * @group Unit
 *
 * @author Roger RAKOTOFIRINGA
 */
final class DataRepositoryUnitTest extends TestCase
{
    public function testIsTrue(): void
    {
        $dataRepository = new DataRepository();
        $dataRepository->setLogo('logo1')
                  ->setServersLocation('serversLocation1')
                  ->setCatopidorLink('catopidorLink1')
                  ->setRe3dataLink('re3dataLink1')
                  ->setCatopidorIdentifier('catopidorIdentifier1')
                  ->setRe3dataIdentifier('re3dataIdentifier1')
                  ->setCertificate('certificate1')
                  ->setRepositoryIdentifier('repositoryIdentifier1')
                  ->setRetentionPeriod('4 year')
                  ->setRepositoryCreationDate('2000')
                  ->setFileVolumeLimit('1 Go')
                  ->setDatasetVolumeLimit('2 Go')
                  ->setDisciplinaryAreas(['discipline1', 'discipline2']);

        $this->assertEquals('logo1', $dataRepository->getLogo());
        $this->assertEquals('serversLocation1', $dataRepository->getServersLocation());
        $this->assertEquals('catopidorLink1', $dataRepository->getCatopidorLink());
        $this->assertEquals('re3dataLink1', $dataRepository->getRe3dataLink());
        $this->assertEquals('catopidorIdentifier1', $dataRepository->getCatopidorIdentifier());
        $this->assertEquals('re3dataIdentifier1', $dataRepository->getRe3dataIdentifier());
        $this->assertEquals('certificate1', $dataRepository->getCertificate());
        $this->assertEquals('repositoryIdentifier1', $dataRepository->getRepositoryIdentifier());
        $this->assertEquals('4 year', $dataRepository->getRetentionPeriod());
        $this->assertEquals('2000', $dataRepository->getRepositoryCreationDate());
        $this->assertEquals('1 Go', $dataRepository->getFileVolumeLimit());
        $this->assertEquals('2 Go', $dataRepository->getDatasetVolumeLimit());
        $this->assertEquals(['discipline1', 'discipline2'], $dataRepository->getDisciplinaryAreas());

        $this->assertIsString($dataRepository->getLogo());
        $this->assertIsString($dataRepository->getServersLocation());
        $this->assertIsString($dataRepository->getCatopidorLink());
        $this->assertIsString($dataRepository->getRe3dataLink());
        $this->assertIsString($dataRepository->getCatopidorIdentifier());
        $this->assertIsString($dataRepository->getRe3dataIdentifier());
        $this->assertIsString($dataRepository->getCertificate());
        $this->assertIsString($dataRepository->getRepositoryIdentifier());
        $this->assertIsString($dataRepository->getRetentionPeriod());
        $this->assertIsString($dataRepository->getRepositoryCreationDate());
        $this->assertIsString($dataRepository->getFileVolumeLimit());
        $this->assertIsString($dataRepository->getDatasetVolumeLimit());
        $this->assertIsArray($dataRepository->getDisciplinaryAreas());
    }

    public function testSupportingInstitutionClass()
    {
        $supportingInstitution = new SupportingInstitutionTranslation();
        $supportingInstitution->setName('Name');
        $this->assertTrue($supportingInstitution->getName() === 'Name');
        $this->assertIsString($supportingInstitution->getName());
    }

    public function testDisciplineClass()
    {
        $discipline = new DisciplineTranslation();
        $discipline->setTitle('Title');
        $this->assertTrue($discipline->getTitle() === 'Title');
        $this->assertIsString($discipline->getTitle());
    }

    public function testKeywordClass()
    {
        $keyword = new KeywordTranslation();
        $keyword->setTerm('Term');
        $this->assertTrue($keyword->getTerm() === 'Term');
        $this->assertIsString($keyword->getTerm());
    }
}
