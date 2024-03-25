<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220608153358 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event ADD intervalle INT DEFAULT NULL, ADD periodicity VARCHAR(50) DEFAULT NULL, ADD repetition_end_date DATE DEFAULT NULL, ADD number_occurrence INT DEFAULT NULL, ADD mass_modification TINYINT(1) DEFAULT NULL, ADD group_id VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP intervalle, DROP periodicity, DROP repetition_end_date, DROP number_occurrence, DROP mass_modification, DROP group_id');
    }
}
