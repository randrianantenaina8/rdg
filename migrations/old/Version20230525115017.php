<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230525115017 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE discipline (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE discipline_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, locale VARCHAR(5) NOT NULL, INDEX IDX_D321EB552C2AC5D3 (translatable_id), UNIQUE INDEX discipline_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE keyword (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE keyword_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, term VARCHAR(255) NOT NULL, locale VARCHAR(5) NOT NULL, INDEX IDX_7D2310AA2C2AC5D3 (translatable_id), UNIQUE INDEX keyword_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE warehouse (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(255) DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, data_type VARCHAR(255) DEFAULT NULL, servers_location VARCHAR(255) DEFAULT NULL, supporting_institution VARCHAR(255) DEFAULT NULL, catopidor_link VARCHAR(255) DEFAULT NULL, re3data_link VARCHAR(255) DEFAULT NULL, catopidor_identifier VARCHAR(255) DEFAULT NULL, re3data_identifier VARCHAR(255) DEFAULT NULL, certificate VARCHAR(255) DEFAULT NULL, embargo VARCHAR(255) DEFAULT NULL, repository_identifier VARCHAR(255) DEFAULT NULL, volume_limit VARCHAR(255) DEFAULT NULL, retention_period VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE warehouse_discipline (warehouse_id INT NOT NULL, discipline_id INT NOT NULL, INDEX IDX_2E5FD56C5080ECDE (warehouse_id), INDEX IDX_2E5FD56CA5522701 (discipline_id), PRIMARY KEY(warehouse_id, discipline_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE warehouse_keyword (warehouse_id INT NOT NULL, keyword_id INT NOT NULL, INDEX IDX_7D74644E5080ECDE (warehouse_id), INDEX IDX_7D74644E115D4552 (keyword_id), PRIMARY KEY(warehouse_id, keyword_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE warehouse_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, locale VARCHAR(5) NOT NULL, INDEX IDX_4650A41A2C2AC5D3 (translatable_id), UNIQUE INDEX warehouse_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE discipline_translation ADD CONSTRAINT FK_D321EB552C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES discipline (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE keyword_translation ADD CONSTRAINT FK_7D2310AA2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES keyword (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE warehouse_discipline ADD CONSTRAINT FK_2E5FD56C5080ECDE FOREIGN KEY (warehouse_id) REFERENCES warehouse (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE warehouse_discipline ADD CONSTRAINT FK_2E5FD56CA5522701 FOREIGN KEY (discipline_id) REFERENCES discipline (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE warehouse_keyword ADD CONSTRAINT FK_7D74644E5080ECDE FOREIGN KEY (warehouse_id) REFERENCES warehouse (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE warehouse_keyword ADD CONSTRAINT FK_7D74644E115D4552 FOREIGN KEY (keyword_id) REFERENCES keyword (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE warehouse_translation ADD CONSTRAINT FK_4650A41A2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES warehouse (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE discipline_translation DROP FOREIGN KEY FK_D321EB552C2AC5D3');
        $this->addSql('ALTER TABLE warehouse_discipline DROP FOREIGN KEY FK_2E5FD56CA5522701');
        $this->addSql('ALTER TABLE keyword_translation DROP FOREIGN KEY FK_7D2310AA2C2AC5D3');
        $this->addSql('ALTER TABLE warehouse_keyword DROP FOREIGN KEY FK_7D74644E115D4552');
        $this->addSql('ALTER TABLE warehouse_discipline DROP FOREIGN KEY FK_2E5FD56C5080ECDE');
        $this->addSql('ALTER TABLE warehouse_keyword DROP FOREIGN KEY FK_7D74644E5080ECDE');
        $this->addSql('ALTER TABLE warehouse_translation DROP FOREIGN KEY FK_4650A41A2C2AC5D3');
        $this->addSql('DROP TABLE discipline');
        $this->addSql('DROP TABLE discipline_translation');
        $this->addSql('DROP TABLE keyword');
        $this->addSql('DROP TABLE keyword_translation');
        $this->addSql('DROP TABLE warehouse');
        $this->addSql('DROP TABLE warehouse_discipline');
        $this->addSql('DROP TABLE warehouse_keyword');
        $this->addSql('DROP TABLE warehouse_translation');
    }
}
