<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201010201443 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add language to the user\'s table';
    }

    public function up(Schema $schema) : void
    {
        $sql = <<<SQL
            ALTER TABLE `user` 
                ADD COLUMN `language` varchar(2) DEFAULT 'en';
SQL;
        $this->addSql($sql);
    }

    public function down(Schema $schema) : void
    {
        $sql = <<<SQL
            ALTER TABLE `user` 
                DROP COLUMN `language`;
SQL;
        $this->addSql($sql);
    }
}
