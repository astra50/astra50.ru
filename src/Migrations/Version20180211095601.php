<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180211095601 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('RENAME TABLE user TO users');

        $this->addSql('ALTER TABLE news DROP FOREIGN KEY FK_1DD39950F675F31B');
        $this->addSql('DROP INDEX IDX_1DD39950F675F31B ON `news`');

        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D7FC21131');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840DA76ED395');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840DBD0F409C');
        $this->addSql('DROP INDEX IDX_6D28840D7FC21131 ON `payment`');
        $this->addSql('DROP INDEX IDX_6D28840DA76ED395 ON `payment`');
        $this->addSql('DROP INDEX IDX_6D28840DBD0F409C ON `payment`');

        $this->addSql('ALTER TABLE area_user DROP FOREIGN KEY FK_4FD6F956A76ED395');
        $this->addSql('ALTER TABLE area_user DROP FOREIGN KEY FK_4FD6F956BD0F409C');
        $this->addSql('DROP INDEX IDX_4FD6F956A76ED395 ON `area_user`');
        $this->addSql('DROP INDEX IDX_4FD6F956BD0F409C ON `area_user`');

        $this->addSql('ALTER TABLE area DROP FOREIGN KEY FK_D7943D6887CF8EB');
        $this->addSql('DROP INDEX IDX_D7943D6887CF8EB ON `area`');

        $this->addSql('ALTER TABLE purpose DROP COLUMN id');
        $this->addSql('ALTER TABLE payment DROP COLUMN id');
        $this->addSql('ALTER TABLE area DROP COLUMN id');
        $this->addSql('DROP TABLE area_user');
        $this->addSql('ALTER TABLE news DROP COLUMN id');
        $this->addSql('ALTER TABLE street DROP COLUMN id');
        $this->addSql('ALTER TABLE users DROP COLUMN id');
        $this->addSql('ALTER TABLE suggestion DROP COLUMN id');

        $this->addSql('CREATE TABLE area_user (area_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_4FD6F956BD0F409C (area_id), INDEX IDX_4FD6F956A76ED395 (user_id), PRIMARY KEY(area_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE area ADD id INT AUTO_INCREMENT NOT NULL FIRST, CHANGE street_id street_id INT DEFAULT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE users ADD id INT AUTO_INCREMENT NOT NULL FIRST, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE news ADD id INT AUTO_INCREMENT NOT NULL FIRST, CHANGE author_id author_id INT DEFAULT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE purpose ADD id INT AUTO_INCREMENT NOT NULL FIRST, CHANGE archived_at archived_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE payment ADD id INT AUTO_INCREMENT NOT NULL FIRST, CHANGE area_id area_id INT DEFAULT NULL, CHANGE purpose_id purpose_id INT DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE street ADD id INT AUTO_INCREMENT NOT NULL FIRST, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE suggestion ADD id INT AUTO_INCREMENT NOT NULL FIRST, ADD PRIMARY KEY (id)');

        $this->addSql('UPDATE news SET author_id = (SELECT id FROM users WHERE email = \'kirillsidorov@gmail.com\')');

        $this->addSql('ALTER TABLE area_user ADD CONSTRAINT FK_4FD6F956BD0F409C FOREIGN KEY (area_id) REFERENCES area (id)');
        $this->addSql('ALTER TABLE area_user ADD CONSTRAINT FK_4FD6F956A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DBD0F409C FOREIGN KEY (area_id) REFERENCES area (id)');
        $this->addSql('ALTER TABLE news ADD CONSTRAINT FK_1DD39950F675F31B FOREIGN KEY (author_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE area ADD CONSTRAINT FK_D7943D6887CF8EB FOREIGN KEY (street_id) REFERENCES street (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D7FC21131 FOREIGN KEY (purpose_id) REFERENCES purpose (id)');
        $this->addSql('CREATE INDEX IDX_D7943D6887CF8EB ON area (street_id)');
        $this->addSql('CREATE INDEX IDX_1DD39950F675F31B ON news (author_id)');
        $this->addSql('CREATE INDEX IDX_6D28840DBD0F409C ON payment (area_id)');
        $this->addSql('CREATE INDEX IDX_6D28840D7FC21131 ON payment (purpose_id)');
        $this->addSql('CREATE INDEX IDX_6D28840DA76ED395 ON payment (user_id)');

        $this->addSql('DROP INDEX uniq_8d93d64992fc23a8 ON users');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E992FC23A8 ON users (username_canonical)');
        $this->addSql('DROP INDEX uniq_8d93d649a0d96fbf ON users');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9A0D96FBF ON users (email_canonical)');
        $this->addSql('DROP INDEX uniq_8d93d649c05fb297 ON users');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9C05FB297 ON users (confirmation_token)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
    }
}
