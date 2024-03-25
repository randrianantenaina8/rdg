<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230606120135 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE dataset_reused (id INT AUTO_INCREMENT NOT NULL, reuse_type_id INT DEFAULT NULL, publication_title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, author VARCHAR(255) DEFAULT NULL, author_affiliation VARCHAR(255) DEFAULT NULL, publication_date DATETIME DEFAULT NULL, dataset_reused_doi VARCHAR(255) DEFAULT NULL, new_dataset_doi VARCHAR(255) DEFAULT NULL, new_dataset_url VARCHAR(255) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, INDEX IDX_988F9DEA35F42213 (reuse_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reuse_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE dataset_reused ADD CONSTRAINT FK_988F9DEA35F42213 FOREIGN KEY (reuse_type_id) REFERENCES reuse_type (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dataset_reused DROP FOREIGN KEY FK_988F9DEA35F42213');
        $this->addSql('DROP TABLE dataset_reused');
        $this->addSql('DROP TABLE reuse_type');
    }
}
