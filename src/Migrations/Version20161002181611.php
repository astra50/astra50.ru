<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161002181611 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D4A3490E4');
        $this->addSql('DROP INDEX IDX_6D28840D4A3490E4 ON payment');
        $this->addSql('ALTER TABLE payment CHANGE payment_purpose_id purpose_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D7FC21131 FOREIGN KEY (purpose_id) REFERENCES payment_purpose (id)');
        $this->addSql('CREATE INDEX IDX_6D28840D7FC21131 ON payment (purpose_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D7FC21131');
        $this->addSql('DROP INDEX IDX_6D28840D7FC21131 ON payment');
        $this->addSql('ALTER TABLE payment CHANGE purpose_id payment_purpose_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D4A3490E4 FOREIGN KEY (payment_purpose_id) REFERENCES payment_purpose (id)');
        $this->addSql('CREATE INDEX IDX_6D28840D4A3490E4 ON payment (payment_purpose_id)');
    }
}
