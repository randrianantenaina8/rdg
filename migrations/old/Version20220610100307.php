<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220610100307 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE dataset_draft (id INT AUTO_INCREMENT NOT NULL, actuality_id INT DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, dataset_id INT DEFAULT NULL, dataset_quote LONGTEXT NOT NULL, image VARCHAR(255) DEFAULT NULL, persistent_id VARCHAR(255) DEFAULT NULL, link_dataverse VARCHAR(512) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_3693E64EB84BD854 (actuality_id), INDEX IDX_3693E64EDE12AB56 (created_by), INDEX IDX_3693E64E16FE72E1 (updated_by), INDEX IDX_3693E64ED47C2D1B (dataset_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE datasets_draft_taxonomies (dataset_draft_id INT NOT NULL, taxonomy_id INT NOT NULL, INDEX IDX_8AE35F8B7D67F358 (dataset_draft_id), INDEX IDX_8AE35F8B9557E6F6 (taxonomy_id), PRIMARY KEY(dataset_draft_id, taxonomy_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dataset_draft_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, hook LONGTEXT NOT NULL, content LONGTEXT NOT NULL, slug VARCHAR(510) NOT NULL, locale VARCHAR(5) NOT NULL, UNIQUE INDEX UNIQ_8BB17029989D9B62 (slug), INDEX IDX_8BB170292C2AC5D3 (translatable_id), UNIQUE INDEX dataset_draft_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE dataset_draft ADD CONSTRAINT FK_3693E64EB84BD854 FOREIGN KEY (actuality_id) REFERENCES actuality (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE dataset_draft ADD CONSTRAINT FK_3693E64EDE12AB56 FOREIGN KEY (created_by) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE dataset_draft ADD CONSTRAINT FK_3693E64E16FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE dataset_draft ADD CONSTRAINT FK_3693E64ED47C2D1B FOREIGN KEY (dataset_id) REFERENCES dataset (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE datasets_draft_taxonomies ADD CONSTRAINT FK_8AE35F8B7D67F358 FOREIGN KEY (dataset_draft_id) REFERENCES dataset_draft (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE datasets_draft_taxonomies ADD CONSTRAINT FK_8AE35F8B9557E6F6 FOREIGN KEY (taxonomy_id) REFERENCES taxonomy (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dataset_draft_translation ADD CONSTRAINT FK_8BB170292C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES dataset_draft (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dataset DROP is_published, DROP published_at');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE datasets_draft_taxonomies DROP FOREIGN KEY FK_8AE35F8B7D67F358');
        $this->addSql('ALTER TABLE dataset_draft_translation DROP FOREIGN KEY FK_8BB170292C2AC5D3');
        $this->addSql('DROP TABLE dataset_draft');
        $this->addSql('DROP TABLE datasets_draft_taxonomies');
        $this->addSql('DROP TABLE dataset_draft_translation');
        $this->addSql('ALTER TABLE dataset ADD is_published TINYINT(1) NOT NULL, ADD published_at DATETIME DEFAULT NULL');
    }
}
