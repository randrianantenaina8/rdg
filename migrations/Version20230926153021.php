<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230926153021 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE data_repository (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, servers_location VARCHAR(255) DEFAULT NULL, catopidor_link VARCHAR(255) DEFAULT NULL, re3data_link VARCHAR(255) DEFAULT NULL, catopidor_identifier VARCHAR(255) DEFAULT NULL, re3data_identifier VARCHAR(255) DEFAULT NULL, certificate VARCHAR(255) DEFAULT NULL, repository_identifier VARCHAR(255) DEFAULT NULL, repository_creation_date VARCHAR(50) DEFAULT NULL, file_volume_limit INT DEFAULT NULL, dataset_volume_limit INT DEFAULT NULL, retention_period VARCHAR(255) DEFAULT NULL, disciplinary_areas LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_93CB6D14DE12AB56 (created_by), INDEX IDX_93CB6D1416FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE data_repository_supporting_institution (data_repository_id INT NOT NULL, supporting_institution_id INT NOT NULL, INDEX IDX_78A7FE01A18BC0DD (data_repository_id), INDEX IDX_78A7FE0164C2C9E2 (supporting_institution_id), PRIMARY KEY(data_repository_id, supporting_institution_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE data_repository_discipline (data_repository_id INT NOT NULL, discipline_id INT NOT NULL, INDEX IDX_B75E0849A18BC0DD (data_repository_id), INDEX IDX_B75E0849A5522701 (discipline_id), PRIMARY KEY(data_repository_id, discipline_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE data_repository_keyword (data_repository_id INT NOT NULL, keyword_id INT NOT NULL, INDEX IDX_D1C23105A18BC0DD (data_repository_id), INDEX IDX_D1C23105115D4552 (keyword_id), PRIMARY KEY(data_repository_id, keyword_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE data_repository_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, url LONGTEXT NOT NULL, data_type VARCHAR(255) DEFAULT NULL, repository_moderation VARCHAR(255) DEFAULT NULL, embargo VARCHAR(255) DEFAULT NULL, locale VARCHAR(5) NOT NULL, INDEX IDX_DCD71802C2AC5D3 (translatable_id), UNIQUE INDEX data_repository_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE discipline (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE discipline_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, locale VARCHAR(5) NOT NULL, INDEX IDX_D321EB552C2AC5D3 (translatable_id), UNIQUE INDEX discipline_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE keyword (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE keyword_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, term VARCHAR(255) NOT NULL, locale VARCHAR(5) NOT NULL, INDEX IDX_7D2310AA2C2AC5D3 (translatable_id), UNIQUE INDEX keyword_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE supporting_institution (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_DD83954EDE12AB56 (created_by), INDEX IDX_DD83954E16FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE supporting_institution_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, locale VARCHAR(5) NOT NULL, INDEX IDX_73BCDA052C2AC5D3 (translatable_id), UNIQUE INDEX supporting_institution_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE data_repository ADD CONSTRAINT FK_93CB6D14DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE data_repository ADD CONSTRAINT FK_93CB6D1416FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE data_repository_supporting_institution ADD CONSTRAINT FK_78A7FE01A18BC0DD FOREIGN KEY (data_repository_id) REFERENCES data_repository (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_repository_supporting_institution ADD CONSTRAINT FK_78A7FE0164C2C9E2 FOREIGN KEY (supporting_institution_id) REFERENCES supporting_institution (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_repository_discipline ADD CONSTRAINT FK_B75E0849A18BC0DD FOREIGN KEY (data_repository_id) REFERENCES data_repository (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_repository_discipline ADD CONSTRAINT FK_B75E0849A5522701 FOREIGN KEY (discipline_id) REFERENCES discipline (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_repository_keyword ADD CONSTRAINT FK_D1C23105A18BC0DD FOREIGN KEY (data_repository_id) REFERENCES data_repository (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_repository_keyword ADD CONSTRAINT FK_D1C23105115D4552 FOREIGN KEY (keyword_id) REFERENCES keyword (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE data_repository_translation ADD CONSTRAINT FK_DCD71802C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES data_repository (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE discipline_translation ADD CONSTRAINT FK_D321EB552C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES discipline (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE keyword_translation ADD CONSTRAINT FK_7D2310AA2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES keyword (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE supporting_institution ADD CONSTRAINT FK_DD83954EDE12AB56 FOREIGN KEY (created_by) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE supporting_institution ADD CONSTRAINT FK_DD83954E16FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE supporting_institution_translation ADD CONSTRAINT FK_73BCDA052C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES supporting_institution (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE data_repository_supporting_institution DROP FOREIGN KEY FK_78A7FE01A18BC0DD');
        $this->addSql('ALTER TABLE data_repository_discipline DROP FOREIGN KEY FK_B75E0849A18BC0DD');
        $this->addSql('ALTER TABLE data_repository_keyword DROP FOREIGN KEY FK_D1C23105A18BC0DD');
        $this->addSql('ALTER TABLE data_repository_translation DROP FOREIGN KEY FK_DCD71802C2AC5D3');
        $this->addSql('ALTER TABLE data_repository_discipline DROP FOREIGN KEY FK_B75E0849A5522701');
        $this->addSql('ALTER TABLE discipline_translation DROP FOREIGN KEY FK_D321EB552C2AC5D3');
        $this->addSql('ALTER TABLE data_repository_keyword DROP FOREIGN KEY FK_D1C23105115D4552');
        $this->addSql('ALTER TABLE keyword_translation DROP FOREIGN KEY FK_7D2310AA2C2AC5D3');
        $this->addSql('ALTER TABLE data_repository_supporting_institution DROP FOREIGN KEY FK_78A7FE0164C2C9E2');
        $this->addSql('ALTER TABLE supporting_institution_translation DROP FOREIGN KEY FK_73BCDA052C2AC5D3');
        $this->addSql('DROP TABLE data_repository');
        $this->addSql('DROP TABLE data_repository_supporting_institution');
        $this->addSql('DROP TABLE data_repository_discipline');
        $this->addSql('DROP TABLE data_repository_keyword');
        $this->addSql('DROP TABLE data_repository_translation');
        $this->addSql('DROP TABLE discipline');
        $this->addSql('DROP TABLE discipline_translation');
        $this->addSql('DROP TABLE keyword');
        $this->addSql('DROP TABLE keyword_translation');
        $this->addSql('DROP TABLE supporting_institution');
        $this->addSql('DROP TABLE supporting_institution_translation');
    }
}
