<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180303140322 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE credential (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, identifier VARCHAR(255) NOT NULL, payloads LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', expired_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL, INDEX IDX_57F1D4BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE credential ADD CONSTRAINT FK_57F1D4BA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('DROP INDEX UNIQ_1483A5E992FC23A8 ON users');
        $this->addSql('DROP INDEX UNIQ_1483A5E9A0D96FBF ON users');
        $this->addSql('DROP INDEX UNIQ_1483A5E9C05FB297 ON users');

        $this->addSql('INSERT INTO credential (user_id, type, identifier, payloads, created_at) SELECT id, \'password\', password, \'a:0:{}\', NOW() FROM users WHERE TRIM(\'password\') <> \'\'');
        $this->addSql('INSERT INTO credential (user_id, type, identifier, payloads, created_at) SELECT id, \'google\', google_id, CONCAT(CONCAT(CONCAT(CONCAT(\'a:1:{s:12:"access_token";s:\', LENGTH(google_access_token)), \':"\'), google_access_token), \'";}\'), NOW() FROM users WHERE google_id IS NOT NULL;');
        $this->addSql('INSERT INTO credential (user_id, type, identifier, payloads, created_at) SELECT id, \'vkontakte\', vkontakte_id, CONCAT(CONCAT(CONCAT(CONCAT(\'a:1:{s:12:"access_token";s:\', LENGTH(vkontakte_access_token)), \':"\'), vkontakte_access_token), \'";}\'), NOW() FROM users WHERE vkontakte_id IS NOT NULL AND vkontakte_id <> \'\'');

        $this->addSql('ALTER TABLE users ADD created_at DATETIME NOT NULL, DROP username_canonical, DROP email, DROP email_canonical, DROP enabled, DROP salt, DROP password, DROP last_login, DROP confirmation_token, DROP password_requested_at, DROP google_id, DROP google_access_token, DROP vkontakte_id, DROP vkontakte_access_token');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9F85E0677 ON users (username)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE credential');
        $this->addSql('DROP INDEX UNIQ_1483A5E9F85E0677 ON users');
        $this->addSql('ALTER TABLE users ADD username_canonical VARCHAR(180) NOT NULL COLLATE utf8_unicode_ci, ADD email VARCHAR(180) NOT NULL COLLATE utf8_unicode_ci, ADD email_canonical VARCHAR(180) NOT NULL COLLATE utf8_unicode_ci, ADD enabled TINYINT(1) NOT NULL, ADD salt VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD password VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD last_login DATETIME DEFAULT NULL, ADD confirmation_token VARCHAR(180) DEFAULT NULL COLLATE utf8_unicode_ci, ADD password_requested_at DATETIME DEFAULT NULL, ADD google_id VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD google_access_token VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD vkontakte_id VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD vkontakte_access_token VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, DROP created_at');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E992FC23A8 ON users (username_canonical)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9A0D96FBF ON users (email_canonical)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9C05FB297 ON users (confirmation_token)');
    }
}