<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230818102645 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE actuality_draft_translation ADD image_locale VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE actuality_translation ADD image_locale VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE dataset_draft_translation ADD image_locale VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE dataset_translation ADD image_locale VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE guide_draft_translation ADD image_locale VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE guide_translation ADD image_locale VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE actuality_draft_translation DROP image_locale');
        $this->addSql('ALTER TABLE actuality_translation DROP image_locale');
        $this->addSql('ALTER TABLE dataset_draft_translation DROP image_locale');
        $this->addSql('ALTER TABLE dataset_translation DROP image_locale');
        $this->addSql('ALTER TABLE guide_draft_translation DROP image_locale');
        $this->addSql('ALTER TABLE guide_translation DROP image_locale');
    }
}
