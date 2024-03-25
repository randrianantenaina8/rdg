<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230526083721 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE logigram (id INT AUTO_INCREMENT NOT NULL, updated_by INT DEFAULT NULL, INDEX IDX_A02A48C816FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE logigramNextStep (id INT AUTO_INCREMENT NOT NULL, logigram_step_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, info LONGTEXT DEFAULT NULL, next_step INT DEFAULT NULL, INDEX IDX_A1E0F513F4D21BB (logigram_step_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE logigramStep (id INT AUTO_INCREMENT NOT NULL, logigram_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, info LONGTEXT DEFAULT NULL, choices LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_974624531AE60EB3 (logigram_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE logigramTranslation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, sub_title LONGTEXT NOT NULL, locale VARCHAR(5) NOT NULL, INDEX IDX_1960AF682C2AC5D3 (translatable_id), UNIQUE INDEX logigramTranslation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE logigram ADD CONSTRAINT FK_A02A48C816FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE logigramNextStep ADD CONSTRAINT FK_A1E0F513F4D21BB FOREIGN KEY (logigram_step_id) REFERENCES logigramStep (id)');
        $this->addSql('ALTER TABLE logigramStep ADD CONSTRAINT FK_974624531AE60EB3 FOREIGN KEY (logigram_id) REFERENCES logigram (id)');
        $this->addSql('ALTER TABLE logigramTranslation ADD CONSTRAINT FK_1960AF682C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES logigram (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE logigramStep DROP FOREIGN KEY FK_974624531AE60EB3');
        $this->addSql('ALTER TABLE logigramTranslation DROP FOREIGN KEY FK_1960AF682C2AC5D3');
        $this->addSql('ALTER TABLE logigramNextStep DROP FOREIGN KEY FK_A1E0F513F4D21BB');
        $this->addSql('DROP TABLE logigram');
        $this->addSql('DROP TABLE logigramNextStep');
        $this->addSql('DROP TABLE logigramStep');
        $this->addSql('DROP TABLE logigramTranslation');
    }
}
