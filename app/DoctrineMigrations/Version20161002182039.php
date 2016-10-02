<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161002182039 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D7FC21131');
        $this->addSql('RENAME TABLE `payment_purpose` TO `purpose`;');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D7FC21131 FOREIGN KEY (purpose_id) REFERENCES purpose (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D7FC21131');
        $this->addSql('RENAME TABLE `purpose` TO `payment_purpose`;');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D7FC21131 FOREIGN KEY (purpose_id) REFERENCES payment_purpose (id)');
    }
}
