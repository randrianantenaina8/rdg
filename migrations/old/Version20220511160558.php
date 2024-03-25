<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220511160558 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE page_draft (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, page_id INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_20FD6942DE12AB56 (created_by), INDEX IDX_20FD694216FE72E1 (updated_by), INDEX IDX_20FD6942C4663E4 (page_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE page_draft_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, slug VARCHAR(510) NOT NULL, locale VARCHAR(5) NOT NULL, UNIQUE INDEX UNIQ_7A56F3C9989D9B62 (slug), INDEX IDX_7A56F3C92C2AC5D3 (translatable_id), UNIQUE INDEX page_draft_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE page_draft ADD CONSTRAINT FK_20FD6942DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE page_draft ADD CONSTRAINT FK_20FD694216FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE page_draft ADD CONSTRAINT FK_20FD6942C4663E4 FOREIGN KEY (page_id) REFERENCES page (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE page_draft_translation ADD CONSTRAINT FK_7A56F3C92C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES page_draft (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE page DROP is_published, DROP published_at');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE page_draft_translation DROP FOREIGN KEY FK_7A56F3C92C2AC5D3');
        $this->addSql('DROP TABLE page_draft');
        $this->addSql('DROP TABLE page_draft_translation');
        $this->addSql('ALTER TABLE page ADD is_published TINYINT(1) NOT NULL, ADD published_at DATETIME DEFAULT NULL');
    }
}
