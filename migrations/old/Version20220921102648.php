<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220921102648 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category CHANGE weight weight INT DEFAULT 10 NOT NULL');
        $this->addSql('ALTER TABLE category_guide CHANGE weight weight INT DEFAULT 10 NOT NULL');
        $this->addSql('ALTER TABLE category_guide_draft CHANGE weight weight INT DEFAULT 10 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category CHANGE weight weight INT NOT NULL');
        $this->addSql('ALTER TABLE category_guide CHANGE weight weight INT NOT NULL');
        $this->addSql('ALTER TABLE category_guide_draft CHANGE weight weight INT NOT NULL');
    }
}
