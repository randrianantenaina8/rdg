<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230215150614 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE project_team (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, weight INT DEFAULT 10 NOT NULL, image VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_FD716E07DE12AB56 (created_by), INDEX IDX_FD716E0716FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_team_draft (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, member_id INT DEFAULT NULL, weight INT DEFAULT 10 NOT NULL, image VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_99DCA4E6DE12AB56 (created_by), INDEX IDX_99DCA4E616FE72E1 (updated_by), INDEX IDX_99DCA4E67597D3FE (member_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_team_draft_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, role VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, img_licence VARCHAR(50) DEFAULT NULL, locale VARCHAR(5) NOT NULL, INDEX IDX_2FC303F12C2AC5D3 (translatable_id), UNIQUE INDEX project_team_draft_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_team_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, role VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, img_licence VARCHAR(50) DEFAULT NULL, locale VARCHAR(5) NOT NULL, INDEX IDX_E2DC694B2C2AC5D3 (translatable_id), UNIQUE INDEX project_team_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE project_team ADD CONSTRAINT FK_FD716E07DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE project_team ADD CONSTRAINT FK_FD716E0716FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE project_team_draft ADD CONSTRAINT FK_99DCA4E6DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE project_team_draft ADD CONSTRAINT FK_99DCA4E616FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE project_team_draft ADD CONSTRAINT FK_99DCA4E67597D3FE FOREIGN KEY (member_id) REFERENCES project_team (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE project_team_draft_translation ADD CONSTRAINT FK_2FC303F12C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES project_team_draft (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_team_translation ADD CONSTRAINT FK_E2DC694B2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES project_team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_team_draft_translation CHANGE role role VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE project_team_translation CHANGE role role VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project_team_draft DROP FOREIGN KEY FK_99DCA4E67597D3FE');
        $this->addSql('ALTER TABLE project_team_translation DROP FOREIGN KEY FK_E2DC694B2C2AC5D3');
        $this->addSql('ALTER TABLE project_team_draft_translation DROP FOREIGN KEY FK_2FC303F12C2AC5D3');
        $this->addSql('DROP TABLE project_team');
        $this->addSql('DROP TABLE project_team_draft');
        $this->addSql('DROP TABLE project_team_draft_translation');
        $this->addSql('DROP TABLE project_team_translation');
        $this->addSql('ALTER TABLE project_team_draft_translation CHANGE role role VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE project_team_translation CHANGE role role VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
