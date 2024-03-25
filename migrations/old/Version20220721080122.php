<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220721080122 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE actuality_draft_translation ADD img_licence VARCHAR(50) DEFAULT NULL, ADD img_legend VARCHAR(150) DEFAULT NULL');
        $this->addSql('ALTER TABLE actuality_translation ADD img_licence VARCHAR(50) DEFAULT NULL, ADD img_legend VARCHAR(150) DEFAULT NULL');
        $this->addSql('ALTER TABLE dataset_draft_translation ADD img_licence VARCHAR(50) DEFAULT NULL, ADD img_legend VARCHAR(150) DEFAULT NULL');
        $this->addSql('ALTER TABLE dataset_translation ADD img_licence VARCHAR(50) DEFAULT NULL, ADD img_legend VARCHAR(150) DEFAULT NULL');
        $this->addSql('ALTER TABLE guide_draft_translation ADD img_licence VARCHAR(50) DEFAULT NULL, ADD img_legend VARCHAR(150) DEFAULT NULL');
        $this->addSql('ALTER TABLE guide_translation ADD img_licence VARCHAR(50) DEFAULT NULL, ADD img_legend VARCHAR(150) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE actuality_draft_translation DROP img_licence, DROP img_legend');
        $this->addSql('ALTER TABLE actuality_translation DROP img_licence, DROP img_legend');
        $this->addSql('ALTER TABLE dataset_draft_translation DROP img_licence, DROP img_legend');
        $this->addSql('ALTER TABLE dataset_translation DROP img_licence, DROP img_legend');
        $this->addSql('ALTER TABLE guide_draft_translation DROP img_licence, DROP img_legend');
        $this->addSql('ALTER TABLE guide_translation DROP img_licence, DROP img_legend');
    }
}
