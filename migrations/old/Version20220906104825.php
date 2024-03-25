<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220906104825 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE s3_file_category (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_E3828A3DE12AB56 (created_by), INDEX IDX_E3828A316FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE s3file (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, s3_file_category INT DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, original_name VARCHAR(255) NOT NULL, image_size INT NOT NULL, mime_type VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_772EF596DE12AB56 (created_by), INDEX IDX_772EF59616FE72E1 (updated_by), INDEX IDX_772EF596E3828A3 (s3_file_category), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE s3_file_category ADD CONSTRAINT FK_E3828A3DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE s3_file_category ADD CONSTRAINT FK_E3828A316FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE s3file ADD CONSTRAINT FK_772EF596DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE s3file ADD CONSTRAINT FK_772EF59616FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE s3file ADD CONSTRAINT FK_772EF596E3828A3 FOREIGN KEY (s3_file_category) REFERENCES s3_file_category (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE s3file DROP FOREIGN KEY FK_772EF596E3828A3');
        $this->addSql('DROP TABLE s3_file_category');
        $this->addSql('DROP TABLE s3file');
    }
}
