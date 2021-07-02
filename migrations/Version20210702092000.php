<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210702092000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE phone_user');
        $this->addSql('ALTER TABLE client CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE created_at created_at DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE phone_user (phone_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_6E97845B3B7323CB (phone_id), INDEX IDX_6E97845BA76ED395 (user_id), PRIMARY KEY(phone_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE phone_user ADD CONSTRAINT FK_6E97845B3B7323CB FOREIGN KEY (phone_id) REFERENCES phone (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE phone_user ADD CONSTRAINT FK_6E97845BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE client CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE user CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }
}
