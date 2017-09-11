<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161002105837 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840DDC058279');
        $this->addSql('RENAME TABLE `payment_type` TO `payment_purpose`;');
        $this->addSql('DROP INDEX IDX_6D28840DDC058279 ON payment');
        $this->addSql('ALTER TABLE payment CHANGE payment_type_id payment_purpose_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D4A3490E4 FOREIGN KEY (payment_purpose_id) REFERENCES payment_purpose (id)');
        $this->addSql('CREATE INDEX IDX_6D28840D4A3490E4 ON payment (payment_purpose_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D4A3490E4');
        $this->addSql('RENAME TABLE `payment_purpose` TO `payment_type`;');
        $this->addSql('DROP INDEX IDX_6D28840D4A3490E4 ON payment');
        $this->addSql('ALTER TABLE payment CHANGE payment_purpose_id payment_type_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DDC058279 FOREIGN KEY (payment_type_id) REFERENCES payment_type (id)');
        $this->addSql('CREATE INDEX IDX_6D28840DDC058279 ON payment (payment_type_id)');
    }
}
