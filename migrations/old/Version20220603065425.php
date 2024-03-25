<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220603065425 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE actuality_draft (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, actuality_id INT DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_9C239C28DE12AB56 (created_by), INDEX IDX_9C239C2816FE72E1 (updated_by), INDEX IDX_9C239C28B84BD854 (actuality_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE actualities_draft_taxonomies (actuality_draft_id INT NOT NULL, taxonomy_id INT NOT NULL, INDEX IDX_E45BA1B32F8C6690 (actuality_draft_id), INDEX IDX_E45BA1B39557E6F6 (taxonomy_id), PRIMARY KEY(actuality_draft_id, taxonomy_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE actuality_draft_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, slug VARCHAR(510) NOT NULL, locale VARCHAR(5) NOT NULL, UNIQUE INDEX UNIQ_AF2F30FD989D9B62 (slug), INDEX IDX_AF2F30FD2C2AC5D3 (translatable_id), UNIQUE INDEX actuality_draft_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE actuality_draft ADD CONSTRAINT FK_9C239C28DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE actuality_draft ADD CONSTRAINT FK_9C239C2816FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE actuality_draft ADD CONSTRAINT FK_9C239C28B84BD854 FOREIGN KEY (actuality_id) REFERENCES actuality (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE actualities_draft_taxonomies ADD CONSTRAINT FK_E45BA1B32F8C6690 FOREIGN KEY (actuality_draft_id) REFERENCES actuality_draft (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE actualities_draft_taxonomies ADD CONSTRAINT FK_E45BA1B39557E6F6 FOREIGN KEY (taxonomy_id) REFERENCES taxonomy (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE actuality_draft_translation ADD CONSTRAINT FK_AF2F30FD2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES actuality_draft (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE actuality DROP is_published, DROP published_at');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE actualities_draft_taxonomies DROP FOREIGN KEY FK_E45BA1B32F8C6690');
        $this->addSql('ALTER TABLE actuality_draft_translation DROP FOREIGN KEY FK_AF2F30FD2C2AC5D3');
        $this->addSql('DROP TABLE actuality_draft');
        $this->addSql('DROP TABLE actualities_draft_taxonomies');
        $this->addSql('DROP TABLE actuality_draft_translation');
        $this->addSql('ALTER TABLE actuality ADD is_published TINYINT(1) NOT NULL, ADD published_at DATETIME DEFAULT NULL');
    }
}
