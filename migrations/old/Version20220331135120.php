<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220331135120 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE faq_highlighted (id INT AUTO_INCREMENT NOT NULL, faqblock_id INT NOT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, weight INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_C46165EC86D4082A (faqblock_id), INDEX IDX_C46165ECDE12AB56 (created_by), INDEX IDX_C46165EC16FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE faq_highlighted ADD CONSTRAINT FK_C46165EC86D4082A FOREIGN KEY (faqblock_id) REFERENCES faq_block (id)');
        $this->addSql('ALTER TABLE faq_highlighted ADD CONSTRAINT FK_C46165ECDE12AB56 FOREIGN KEY (created_by) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE faq_highlighted ADD CONSTRAINT FK_C46165EC16FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE faq_block DROP weight');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE faq_highlighted');
        $this->addSql('ALTER TABLE faq_block ADD weight INT NOT NULL');
    }
}
