<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161002182039 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D7FC21131');
        $this->addSql('RENAME TABLE `payment_purpose` TO `purpose`;');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D7FC21131 FOREIGN KEY (purpose_id) REFERENCES purpose (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D7FC21131');
        $this->addSql('RENAME TABLE `purpose` TO `payment_purpose`;');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D7FC21131 FOREIGN KEY (purpose_id) REFERENCES payment_purpose (id)');
    }
}
