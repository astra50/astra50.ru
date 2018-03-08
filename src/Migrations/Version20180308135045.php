<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180308135045 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('UPDATE payment SET comment = \'# Ежемесячные взносы за ЯНВАРЬ\' WHERE user_id = 7 AND purpose_id = 2 AND comment IS NULL;');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
