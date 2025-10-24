<?php
class UserModel {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }
    
    public function closeConnection() {
        $this->db->closeConnection();
    }

    public function getUsers() {
        $this->db->query(
            "SELECT u.id,
                CONCAT(u.first_name, ' ', u.last_name) AS user_name,
                u.username,
                e.id AS employee_id,
                IF(u.employee_id IS NULL,
                    'Sin asignar',
                    CONCAT(e.first_name, ' ', e.last_name)
                ) AS employee_name,
                u.`active`,
                r.id AS role_id,
                r.`name` AS role_name
            FROM `user` AS u
            LEFT JOIN `role` AS r
            ON u.role_id = r.id
            AND r.deleted_at IS NULL
            LEFT JOIN `employee` AS e
            ON u.employee_id = e.id
            AND e.deleted_at IS NULL
            WHERE u.deleted_at IS NULL;"
        );

        $row = $this->db->records();
        return $row;
    }

    public function filterUser($data) {
        $this->db->query(
            "SELECT u.id,
                u.first_name,
                u.last_name,
                u.username,
                e.id AS employee_id,
                CONCAT(e.id, ' - ', e.first_name, ' ', e.last_name) AS employee_name,
                u.`active`,
                r.id AS role_id,
                r.`name` AS role_name
            FROM `user` AS u
            LEFT JOIN `role` AS r
            ON u.role_id = r.id
            AND r.active = 1
            AND r.deleted_at IS NULL
            LEFT JOIN `employee` AS e
            ON u.employee_id = e.id
            AND e.active = 1
            AND e.deleted_at IS NULL
            WHERE u.deleted_at IS NULL
            AND u.id = :id
            LIMIT 1;"
        );

        $this->db->bind(":id", $data["id"]);
        $row = $this->db->record();
        return $row;
    }

    public function searchUsername($data) {
        $this->db->query(
            "SELECT u.username
            FROM `user` AS u
            WHERE u.deleted_at IS NULL
            AND u.username = :username
            LIMIT 1;"
        );

        $this->db->bind(":username", $data["username"]);
        $row = $this->db->record();
        return $row;
    }

    public function insertUser($data) {
        $this->db->query(
            "INSERT INTO `user` (role_id, first_name, last_name, username, employee_id, password, active, created_by, updated_by)
            VALUES (:role_id, :first_name, :last_name, :username, :employee_id, SHA2(:password , 256), :active, :user_id, :user_id);"
        );
        
        $this->db->bind(":role_id", $data["role_id"]);
        $this->db->bind(":first_name", $data["first_name"]);
        $this->db->bind(":last_name", $data["last_name"]);
        $this->db->bind(":username", $data["username"]);
        $this->db->bind(":employee_id", $data["employee_id"]);
        $this->db->bind(":password", $data["password"]);
        $this->db->bind(":active", $data["active"]);
        $this->db->bind(":user_id", $data["user_id"]);
        if($this->db->execute()){
            return true;
        } else{
            return false;
        }
    }

    public function updateUser($data) {
        $this->db->query(
            "UPDATE `user`
            SET role_id = :role_id,
            first_name = :first_name,
            last_name = :last_name,
            employee_id = :employee_id,
            `password` = IF(:password IS NULL, `password`, SHA2(:password, 256)),
            `active` = :active,
            updated_by = :user_id
            WHERE id = :id;"
        );
        
        $this->db->bind(":role_id", $data["role_id"]);
        $this->db->bind(":first_name", $data["first_name"]);
        $this->db->bind(":last_name", $data["last_name"]);
        $this->db->bind(":employee_id", $data["employee_id"]);
        $this->db->bind(":password", $data["password"]);
        $this->db->bind(":active", $data["active"]);
        $this->db->bind(":user_id", $data["user_id"]);
        $this->db->bind(":id", $data["id"]);
        if($this->db->execute()){
            return true;
        } else{
            return false;
        }
    }

    public function deleteUser($data) {
        $this->db->query(
            "UPDATE `user`
            SET deleted_at = CURRENT_TIMESTAMP(),
            deleted_by = :user_id
            WHERE id = :id;"
        );

        $this->db->bind(":user_id", $data["user_id"]);
        $this->db->bind(":id", $data["id"]);

        if($this->db->execute()){
            return true;
        } else{
            return false;
        }
    }
}
?>