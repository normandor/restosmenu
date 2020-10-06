<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201006042342 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create visits table';
    }

    public function up(Schema $schema) : void
    {
        $sql = <<<SQL
            ALTER TABLE `page_visits` 
                ADD COLUMN `restaurant_id` int(5) DEFAULT NULL,
                ADD COLUMN `referer` varchar(250) NOT NULL DEFAULT '',
                ADD COLUMN `agent` varchar(250) NOT NULL DEFAULT '';
SQL;
        $this->addSql($sql);
    }

    public function down(Schema $schema) : void
    {
        $sql = <<<SQL
            ALTER TABLE `page_visits` 
                DROP COLUMN `restaurant_id`,
                DROP COLUMN `referer`,
                DROP COLUMN `agent`;
SQL;
        $this->addSql($sql);
    }
}
