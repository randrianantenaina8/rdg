<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220510135914 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dataworkshop DROP FOREIGN KEY FK_8B2CB54816FE72E1');
        $this->addSql('ALTER TABLE dataworkshop DROP FOREIGN KEY FK_8B2CB548DE12AB56');
        $this->addSql('ALTER TABLE dataworkshop ADD CONSTRAINT FK_8B2CB54816FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE dataworkshop ADD CONSTRAINT FK_8B2CB548DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE institution DROP FOREIGN KEY FK_3A9F98E516FE72E1');
        $this->addSql('ALTER TABLE institution DROP FOREIGN KEY FK_3A9F98E5DE12AB56');
        $this->addSql('ALTER TABLE institution ADD CONSTRAINT FK_3A9F98E516FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE institution ADD CONSTRAINT FK_3A9F98E5DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dataworkshop DROP FOREIGN KEY FK_8B2CB548DE12AB56');
        $this->addSql('ALTER TABLE dataworkshop DROP FOREIGN KEY FK_8B2CB54816FE72E1');
        $this->addSql('ALTER TABLE dataworkshop ADD CONSTRAINT FK_8B2CB548DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE dataworkshop ADD CONSTRAINT FK_8B2CB54816FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE institution DROP FOREIGN KEY FK_3A9F98E5DE12AB56');
        $this->addSql('ALTER TABLE institution DROP FOREIGN KEY FK_3A9F98E516FE72E1');
        $this->addSql('ALTER TABLE institution ADD CONSTRAINT FK_3A9F98E5DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE institution ADD CONSTRAINT FK_3A9F98E516FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
    }
}
