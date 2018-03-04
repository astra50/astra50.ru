<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180304142240 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE purpose_area (purpose_id INT NOT NULL, area_id INT NOT NULL, INDEX IDX_1214737C7FC21131 (purpose_id), INDEX IDX_1214737CBD0F409C (area_id), PRIMARY KEY(purpose_id, area_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE purpose_area ADD CONSTRAINT FK_1214737C7FC21131 FOREIGN KEY (purpose_id) REFERENCES purpose (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE purpose_area ADD CONSTRAINT FK_1214737CBD0F409C FOREIGN KEY (area_id) REFERENCES area (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE purpose CHANGE schedule schedule SMALLINT NOT NULL COMMENT \'(DC2Type:schedule_enum)\', CHANGE calculation calculation SMALLINT NOT NULL COMMENT \'(DC2Type:calculation_enum)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE purpose_area');
        $this->addSql('ALTER TABLE purpose CHANGE schedule schedule SMALLINT NOT NULL, CHANGE calculation calculation SMALLINT NOT NULL');
    }
}
