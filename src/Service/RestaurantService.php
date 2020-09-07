<?php

namespace App\Service;

use Doctrine\DBAL\DBALException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Classes\SSP;

class RestaurantService
{
    private $translator;
    private $COL_POSITION_name = 0;
    private $COL_POSITION_slug = 1;
    private $COL_POSITION_enabled = 2;
    private $COL_POSITION_icons = 3;
    private $COL_POSITION_id = 3;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
        $this->translator = $translator;
        $this->entityManager = $entityManager;
    }

    /**
     * @param string  $dbuser
     * @param string  $dbpw
     * @param string  $dbname
     * @param string  $dbhost
     * @param array   $get
     *
     * @return array
     */
    public function getTableData($dbuser, $dbpw, $dbname, $dbhost, $get): array
    {
        $sql = <<<EOT
           (
                SELECT 
                    restaurant.id, restaurant.name, restaurant.slug, restaurant.enabled,
                    '' as empty
                FROM 
                    restaurant 
                WHERE 
                    restaurant.enabled = 1 
                ORDER BY 
                    restaurant.name
           
    ) temp 
EOT;

        $primaryKey = 'id';
        $columns = [
            ['db' => 'name', 'dt' => $this->COL_POSITION_name],
            ['db' => 'slug', 'dt' => $this->COL_POSITION_slug],
            ['db' => 'enabled', 'dt' => $this->COL_POSITION_enabled],
            ['db' => 'id', 'dt' => 3],
        ];
        $sql_details = [
            'user' => $dbuser,
            'pass' => $dbpw,
            'db' => $dbname,
            'host' => $dbhost,
        ];

        $mysqlResp = SSP::simple($get, $sql_details, $sql, $primaryKey, $columns);

        return $this->addLastColumnIcons($mysqlResp);
    }

    /**
     * @param array $dataArray
     *
     * @return array
     */
    private function addLastColumnIcons($dataArray)
    {
        foreach ($dataArray['data'] as $i => $iValue) {
            $id = $dataArray['data'][$i][$this->COL_POSITION_id];
            $name = $iValue[$this->COL_POSITION_name] . ', ' . $iValue[$this->COL_POSITION_slug];
            $dataArray['data'][$i][$this->COL_POSITION_icons] = '<span style="white-space: nowrap">';
            $dataArray['data'][$i][$this->COL_POSITION_icons] .= $this->addIconEditRestaurant($id);
            $dataArray['data'][$i][$this->COL_POSITION_icons] .= $this->addIconDeleteRestaurant($id, $name);
            $dataArray['data'][$i][$this->COL_POSITION_icons] .= '</span>';
        }

        return $dataArray;
    }

    /**
     * @param int $id
     *
     * @return string|null
     */
    private function addIconDeleteRestaurant($id, $name)
    {
// if (canDelete($_SESSION['username_id'])) {
        return "&nbsp<img height='30' src='../images/icono_delete.png' "
                . "style='cursor: pointer;' onclick='showDeleteUser(" . $id . ", \"" . $name . "\");' "
                . "title='" . $this->translator->trans('deleteuser') . "'>";
    }

    /**
     * @param int $id
     *
     * @return string|null
     */
    private function addIconEditRestaurant($id)
    {
//    if (canEdit($_SESSION['username_id'])) {
        return "&nbsp<img height='30' src='../images/icono_edit.png' "
                . "style='cursor: pointer;' onclick='showEditUser(" . $id . ");' "
                . "title='" . $this->translator->trans('edituser') . "'>";
    }

    /**
     * @param int $id
     *
     * @return array
     *
     * @throws DBALException
     */
    public function getRestaurantData($id)
    {
        $conn = $this->entityManager->getConnection();

        $incId = filter_var($id, FILTER_SANITIZE_NUMBER_INT);

        $sql = <<<EOT
            SELECT 
               `name`, enabled, slug, qr_url 
            FROM 
                restaurant 
            WHERE 
                id = :restaurantId;
EOT;

        $stmt = $conn->prepare($sql);
        $stmt->execute(['restaurantId' => $incId]);

        return $stmt->fetch();
    }

    /**
     * @param String $slug
     *
     * @return bool
     *
     * @throws DBALException
     */
    public function slugExists($slug): bool
    {
        $conn = $this->entityManager->getConnection();

        $sql = <<<EOT
            SELECT count(*) as cnt FROM restaurant WHERE slug = :slug
EOT;

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':slug', $slug);
        $stmt->execute();

        $row = $stmt->fetch();

        return $row['cnt'] > 0;
    }
}
