<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220420145356 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE center_map_coord (id INT AUTO_INCREMENT NOT NULL, center_map_lame_id INT DEFAULT NULL, institution_id INT DEFAULT NULL, dataworkshop_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, x VARCHAR(20) NOT NULL, y VARCHAR(20) NOT NULL, INDEX IDX_E7771C2346E38CA7 (center_map_lame_id), INDEX IDX_E7771C2310405986 (institution_id), INDEX IDX_E7771C23B3A22FDD (dataworkshop_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE center_map_coord ADD CONSTRAINT FK_E7771C2346E38CA7 FOREIGN KEY (center_map_lame_id) REFERENCES lame_center_map (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE center_map_coord ADD CONSTRAINT FK_E7771C2310405986 FOREIGN KEY (institution_id) REFERENCES institution (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE center_map_coord ADD CONSTRAINT FK_E7771C23B3A22FDD FOREIGN KEY (dataworkshop_id) REFERENCES dataworkshop (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE center_map_coord');
    }
}
