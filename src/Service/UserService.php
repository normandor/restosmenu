<?php

namespace App\Service;

use Symfony\Contracts\Translation\TranslatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Classes\SSP;
use PDO;

class UserService
{

    private $translator;
    private $COL_POSITION_lastname = 0;
    private $COL_POSITION_firstname = 1;
    private $COL_POSITION_username = 2;
    private $COL_POSITION_email = 3;
    private $COL_POSITION_roles = 4;
    private $COL_POSITION_active = 5;
    private $COL_POSITION_access_panel = 6;
    private $COL_POSITION_icons = 7;
    private $COL_POSITION_id = 7;   // === icons but will be deleted
    private $entityManager;
    private $utcOffset;

    public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
        $this->translator = $translator;
        $this->entityManager = $entityManager;
        $this->utcOffset = -3;
    }

    /**
     * @param string  $dbuser
     * @param string  $dbpw
     * @param string  $dbname
     * @param string  $dbhost
     * @param array   $get
     *
     * @return Response
     */
    public function getTableData($dbuser, $dbpw, $dbname, $dbhost, $get)
    {
        $sql = <<<EOT
           (
                SELECT 
                    user.id, user.lastname, user.firstname,
                    user.username, user.email, user.verified,
                    user.access_panel, '' as empty
                FROM 
                    user 
                WHERE 
                    user.deleted = 0 
                ORDER BY 
                    lastname, firstname
           
    ) temp 
EOT;

        $primaryKey = 'id';
        $columns = [
            ['db' => 'lastname', 'dt' => $this->COL_POSITION_lastname],
            ['db' => 'firstname', 'dt' => $this->COL_POSITION_firstname],
            ['db' => 'username', 'dt' => $this->COL_POSITION_username],
            ['db' => 'email', 'dt' => $this->COL_POSITION_email],
            ['db' => 'email', 'dt' => $this->COL_POSITION_roles],
            ['db' => 'verified', 'dt' => $this->COL_POSITION_active],
            ['db' => 'access_panel', 'dt' => $this->COL_POSITION_access_panel],
            ['db' => 'id', 'dt' => $this->COL_POSITION_icons],
        ];
        $sql_details = [
            'user' => $dbuser,
            'pass' => $dbpw,
            'db' => $dbname,
            'host' => $dbhost,
        ];

        $mysqlResp = SSP::simple($get, $sql_details, $sql, $primaryKey, $columns);
        $mysqlRespWithIcon = $this->addLastColumnIcons($mysqlResp);

        return $mysqlRespWithIcon;
    }

    /**
     * @param array $dataArray
     *
     * @return array
     */
    private function addLastColumnIcons($dataArray)
    {

        for ($i = 0; $i < sizeof($dataArray['data']); $i++)
        {
            $id = $dataArray['data'][$i][$this->COL_POSITION_id];
            $name = $dataArray['data'][$i][$this->COL_POSITION_lastname] . ', ' . $dataArray['data'][$i][$this->COL_POSITION_firstname];
            $dataArray['data'][$i][$this->COL_POSITION_icons] = '<span style="white-space: nowrap">';
            $dataArray['data'][$i][$this->COL_POSITION_icons] .= $this->addIconEditUser($id);
            $dataArray['data'][$i][$this->COL_POSITION_icons] .= $this->addIconDeleteUser($id, $name);
            $dataArray['data'][$i][$this->COL_POSITION_icons] .= '</span>';
        }

        return $dataArray;
    }

    /**
     * @param int $id
     *
     * @return string|null
     */
    private function addIconDeleteUser($id, $name)
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
    private function addIconEditUser($id)
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
     */
    public function getUserData($id)
    {
        $conn = $this->entityManager->getConnection();

        $incId = filter_var($id, FILTER_SANITIZE_NUMBER_INT);

        $sql = <<<EOT
            SELECT 
                username, firstname, lastname, 
                email, roles, password, 
                verified, avatar_path, access_panel, 
                deleted
            FROM 
                user 
            WHERE 
                user.id = :userId;
EOT;

        $stmt = $conn->prepare($sql);
        $stmt->execute(['userId' => $incId]);
        $result = $stmt->fetch();

        return $result;
    }

    /**
     * @param String $username
     *
     * @return array
     */
    public function usernameExists($username)
    {
        $conn = $this->entityManager->getConnection();

        $sql = <<<EOT
            SELECT count(*) as cnt FROM user WHERE username = :username AND deleted = 0
EOT;

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $row = $stmt->fetch();
        if ($row['cnt'] == 0) {
            return false;
        }

        return true;
    }

    /**
     * @param String $lastname
     * @param String $firstname
     * @param String $email
     * @param String $roles
     * @param String $zones
     * @param int    $acceso_tablero
     * @param int    $usuario_activo
     * @param int    $userid
     *
     * @return array
     */
    public function updateUser($lastname, $firstname, $email, $roles, $zones, $acceso_tablero, $usuario_activo, $userid)
    {
        $conn = $this->entityManager->getConnection();

        $sql = "UPDATE user SET firstname = :firstname, lastname = :lastname, verified = :verified, access_panel = :access_panel, email = :email WHERE id = :userid";
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':userid', $userid);
        $stmt->bindParam(':firstname', $firstname);
        $stmt->bindParam(':lastname', $lastname);
        $stmt->bindParam(':verified', $usuario_activo);
        $stmt->bindParam(':access_panel', $acceso_tablero);
        $stmt->bindParam(':email', $email);

        $stmt->execute();

        return true;
    }

    /**
     * @param int    $userid
     *
     * @return array
     */
    public function deleteUser($userid)
    {
        $conn = $this->entityManager->getConnection();

        $sql = "UPDATE user set deleted = 1 WHERE id = :user_id AND deleted = 0";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $userid);
        $stmt->execute();

        return true;
    }

    /**
     *
     * @param type $username
     * @param type $password
     * @param type $firstname
     * @param type $lastname
     * @param type $email
     *
     * @return boolean
     */
    public function addUser($username, $password, $firstname, $lastname, $email)
    {
        $conn = $this->entityManager->getConnection();

        $avatar_path = "img_uploads/avatar_gen.png";
        $roles = "{}";

        $sql = <<<EOT
            INSERT INTO 
                user 
                    (username, password, firstname, lastname, email, avatar_path, roles) 
                VALUES 
                    (:username, :password, :firstname, :lastname, :email, :avatar_path, :roles)
EOT;

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':firstname', $firstname);
        $stmt->bindParam(':lastname', $lastname);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':avatar_path', $avatar_path);
        $stmt->bindParam(':roles', $roles);

        $stmt->execute();

        return true;
    }
}
