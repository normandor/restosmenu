<?php
namespace App\Tests\Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Doctrine\DBAL\DBALException;

class Api extends \Codeception\Module
{
    /**
     * Delete entries from $table where $criteria conditions
     * Use: $I->deleteFromDatabase('users', ['id' => '111111', 'banned' => 'yes']);
     *
     * @param  string $table    tablename
     * @param  array $criteria conditions. See seeInDatabase() method.
     * @return boolean Returns TRUE on success or FALSE on failure.
     */
    public function deleteFromDatabase($table, $criteria)
    {
        $dbh = $this->getModule('Db')->dbh;

        $query = "delete from %s where %s";
        $params = [];
        foreach ($criteria as $k => $v) {
            $params[] = "$k = ?";
        }
        $params = implode(' AND ', $params);
        $query = sprintf($query, $table, $params);
        $this->debugSection('Query', $query, json_encode($criteria));
        $sth = $dbh->prepare($query);

        return $sth->execute(array_values($criteria));
    }

    public function deleteFromTable($table)
    {
        $dbh = $this->getModule('Db')->dbh;

        $query = sprintf('delete from %s where 1', $table);

        $sth = $dbh->prepare($query);

        return $sth->execute();
    }
}
