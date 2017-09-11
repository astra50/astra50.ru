<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160925161901 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE payment (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary)\', area_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', payment_type_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', user_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', comment LONGTEXT DEFAULT NULL, sum INT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_6D28840DBD0F409C (area_id), INDEX IDX_6D28840DDC058279 (payment_type_id), INDEX IDX_6D28840DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment_type (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary)\', name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, archived_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DBD0F409C FOREIGN KEY (area_id) REFERENCES area (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DDC058279 FOREIGN KEY (payment_type_id) REFERENCES payment_type (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840DDC058279');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE payment_type');
    }
}
