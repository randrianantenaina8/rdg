<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220405150718 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lame_news ADD actu_fourth INT DEFAULT NULL, ADD event_fourth INT DEFAULT NULL, ADD event_fifth INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lame_news ADD CONSTRAINT FK_74936BA19A0E66C7 FOREIGN KEY (actu_fourth) REFERENCES actuality (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE lame_news ADD CONSTRAINT FK_74936BA1453A6322 FOREIGN KEY (event_fourth) REFERENCES event (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE lame_news ADD CONSTRAINT FK_74936BA193059E5F FOREIGN KEY (event_fifth) REFERENCES event (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_74936BA19A0E66C7 ON lame_news (actu_fourth)');
        $this->addSql('CREATE INDEX IDX_74936BA1453A6322 ON lame_news (event_fourth)');
        $this->addSql('CREATE INDEX IDX_74936BA193059E5F ON lame_news (event_fifth)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lame_news DROP FOREIGN KEY FK_74936BA19A0E66C7');
        $this->addSql('ALTER TABLE lame_news DROP FOREIGN KEY FK_74936BA1453A6322');
        $this->addSql('ALTER TABLE lame_news DROP FOREIGN KEY FK_74936BA193059E5F');
        $this->addSql('DROP INDEX IDX_74936BA19A0E66C7 ON lame_news');
        $this->addSql('DROP INDEX IDX_74936BA1453A6322 ON lame_news');
        $this->addSql('DROP INDEX IDX_74936BA193059E5F ON lame_news');
        $this->addSql('ALTER TABLE lame_news DROP actu_fourth, DROP event_fourth, DROP event_fifth');
    }
}
