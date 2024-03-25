<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220617094349 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category_guide_draft (id INT AUTO_INCREMENT NOT NULL, guide_draft_id INT NOT NULL, category_id INT NOT NULL, weight INT NOT NULL, INDEX IDX_B5FA1FB7D7727BB1 (guide_draft_id), INDEX IDX_B5FA1FB712469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE guide_draft (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, guide_id INT DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_2615DDCDE12AB56 (created_by), INDEX IDX_2615DDC16FE72E1 (updated_by), INDEX IDX_2615DDCD7ED1D4B (guide_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE guide_draft_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT DEFAULT NULL, slug VARCHAR(510) NOT NULL, locale VARCHAR(5) NOT NULL, UNIQUE INDEX UNIQ_109B5817989D9B62 (slug), INDEX IDX_109B58172C2AC5D3 (translatable_id), UNIQUE INDEX guide_draft_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE additional_help_guide_draft (id INT AUTO_INCREMENT NOT NULL, guide_draft_id INT NOT NULL, additional_help_id INT NOT NULL, weight INT NOT NULL, INDEX IDX_48376C48D7727BB1 (guide_draft_id), INDEX IDX_48376C48D27BB613 (additional_help_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category_guide_draft ADD CONSTRAINT FK_B5FA1FB7D7727BB1 FOREIGN KEY (guide_draft_id) REFERENCES guide_draft (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_guide_draft ADD CONSTRAINT FK_B5FA1FB712469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE guide_draft ADD CONSTRAINT FK_2615DDCDE12AB56 FOREIGN KEY (created_by) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE guide_draft ADD CONSTRAINT FK_2615DDC16FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE guide_draft ADD CONSTRAINT FK_2615DDCD7ED1D4B FOREIGN KEY (guide_id) REFERENCES guide (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE guide_draft_translation ADD CONSTRAINT FK_109B58172C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES guide_draft (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE additional_help_guide_draft ADD CONSTRAINT FK_48376C48D7727BB1 FOREIGN KEY (guide_draft_id) REFERENCES guide_draft (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE additional_help_guide_draft ADD CONSTRAINT FK_48376C48D27BB613 FOREIGN KEY (additional_help_id) REFERENCES additional_help (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE guide DROP is_published, DROP published_at');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category_guide_draft DROP FOREIGN KEY FK_B5FA1FB7D7727BB1');
        $this->addSql('ALTER TABLE guide_draft_translation DROP FOREIGN KEY FK_109B58172C2AC5D3');
        $this->addSql('DROP TABLE category_guide_draft');
        $this->addSql('DROP TABLE guide_draft');
        $this->addSql('DROP TABLE guide_draft_translation');
        $this->addSql('DROP TABLE additional_help_guide_draft');
        $this->addSql('ALTER TABLE guide ADD is_published TINYINT(1) NOT NULL, ADD published_at DATETIME DEFAULT NULL');
    }
}
