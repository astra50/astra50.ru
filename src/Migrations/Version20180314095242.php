<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180314095242 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('DELETE credential FROM credential WHERE TRIM(identifier) = \'\'');
    }

    public function down(Schema $schema): void
    {
    }
}
