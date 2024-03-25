<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221017084809 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE lame_spotlights_datasets');
        $this->addSql('ALTER TABLE lame_spotlight ADD dataset_first INT DEFAULT NULL, ADD dataset_second INT DEFAULT NULL, ADD dataset_third INT DEFAULT NULL, ADD dataset_fourth INT DEFAULT NULL, ADD dataset_fifth INT DEFAULT NULL, ADD dataset_sixth INT DEFAULT NULL, ADD auto_dataset TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE lame_spotlight ADD CONSTRAINT FK_5E9C58F0E29E9E8D FOREIGN KEY (dataset_first) REFERENCES dataset (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE lame_spotlight ADD CONSTRAINT FK_5E9C58F0D069C5D3 FOREIGN KEY (dataset_second) REFERENCES dataset (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE lame_spotlight ADD CONSTRAINT FK_5E9C58F054DD50BE FOREIGN KEY (dataset_third) REFERENCES dataset (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE lame_spotlight ADD CONSTRAINT FK_5E9C58F011D5C0CA FOREIGN KEY (dataset_fourth) REFERENCES dataset (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE lame_spotlight ADD CONSTRAINT FK_5E9C58F0A2F15FA9 FOREIGN KEY (dataset_fifth) REFERENCES dataset (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE lame_spotlight ADD CONSTRAINT FK_5E9C58F01C49C921 FOREIGN KEY (dataset_sixth) REFERENCES dataset (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_5E9C58F0E29E9E8D ON lame_spotlight (dataset_first)');
        $this->addSql('CREATE INDEX IDX_5E9C58F0D069C5D3 ON lame_spotlight (dataset_second)');
        $this->addSql('CREATE INDEX IDX_5E9C58F054DD50BE ON lame_spotlight (dataset_third)');
        $this->addSql('CREATE INDEX IDX_5E9C58F011D5C0CA ON lame_spotlight (dataset_fourth)');
        $this->addSql('CREATE INDEX IDX_5E9C58F0A2F15FA9 ON lame_spotlight (dataset_fifth)');
        $this->addSql('CREATE INDEX IDX_5E9C58F01C49C921 ON lame_spotlight (dataset_sixth)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE lame_spotlights_datasets (spot_light_lame_id INT NOT NULL, dataset_id INT NOT NULL, INDEX IDX_7954E61EAA95B13C (spot_light_lame_id), INDEX IDX_7954E61ED47C2D1B (dataset_id), PRIMARY KEY(spot_light_lame_id, dataset_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE lame_spotlights_datasets ADD CONSTRAINT FK_7954E61EAA95B13C FOREIGN KEY (spot_light_lame_id) REFERENCES lame_spotlight (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE lame_spotlights_datasets ADD CONSTRAINT FK_7954E61ED47C2D1B FOREIGN KEY (dataset_id) REFERENCES dataset (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE lame_spotlight DROP FOREIGN KEY FK_5E9C58F0E29E9E8D');
        $this->addSql('ALTER TABLE lame_spotlight DROP FOREIGN KEY FK_5E9C58F0D069C5D3');
        $this->addSql('ALTER TABLE lame_spotlight DROP FOREIGN KEY FK_5E9C58F054DD50BE');
        $this->addSql('ALTER TABLE lame_spotlight DROP FOREIGN KEY FK_5E9C58F011D5C0CA');
        $this->addSql('ALTER TABLE lame_spotlight DROP FOREIGN KEY FK_5E9C58F0A2F15FA9');
        $this->addSql('ALTER TABLE lame_spotlight DROP FOREIGN KEY FK_5E9C58F01C49C921');
        $this->addSql('DROP INDEX IDX_5E9C58F0E29E9E8D ON lame_spotlight');
        $this->addSql('DROP INDEX IDX_5E9C58F0D069C5D3 ON lame_spotlight');
        $this->addSql('DROP INDEX IDX_5E9C58F054DD50BE ON lame_spotlight');
        $this->addSql('DROP INDEX IDX_5E9C58F011D5C0CA ON lame_spotlight');
        $this->addSql('DROP INDEX IDX_5E9C58F0A2F15FA9 ON lame_spotlight');
        $this->addSql('DROP INDEX IDX_5E9C58F01C49C921 ON lame_spotlight');
        $this->addSql('ALTER TABLE lame_spotlight DROP dataset_first, DROP dataset_second, DROP dataset_third, DROP dataset_fourth, DROP dataset_fifth, DROP dataset_sixth, DROP auto_dataset');
    }
}
