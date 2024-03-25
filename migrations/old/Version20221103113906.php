<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221103113906 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE subject_recipient (subject_id INT NOT NULL, recipient_id INT NOT NULL, INDEX IDX_2B556F6323EDC87 (subject_id), INDEX IDX_2B556F63E92F8F78 (recipient_id), PRIMARY KEY(subject_id, recipient_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recipient (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE subject_recipient ADD CONSTRAINT FK_2B556F6323EDC87 FOREIGN KEY (subject_id) REFERENCES contact_subject (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subject_recipient ADD CONSTRAINT FK_2B556F63E92F8F78 FOREIGN KEY (recipient_id) REFERENCES recipient (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subject_recipient DROP FOREIGN KEY FK_2B556F6323EDC87');
        $this->addSql('ALTER TABLE subject_recipient DROP FOREIGN KEY FK_2B556F63E92F8F78');
        $this->addSql('DROP TABLE subject_recipient');
        $this->addSql('DROP TABLE recipient');
    }
}
