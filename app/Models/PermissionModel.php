<?php
class PermissionModel{
    private $db;

    public function __construct() {
        $this->db = new Database;
    }
    
    public function closeConnection() {
        $this->db->closeConnection();
    }

    public function filterPermissions($data) {
        $this->db->query(
            "SELECT p.id,
                p.module_id,
                m.`name` AS module,
                m.`order`,
                p.show_operation,
                p.create_operation,
                p.update_operation,
                p.delete_operation,
                m.cud_operation
            FROM `permission` AS p
            INNER JOIN `module` AS m
            ON p.module_id = m.id
            WHERE p.deleted_at IS NULL
            AND m.deleted_at IS NULL
            AND p.role_id = :role_id

            UNION ALL

            SELECT 0 AS id,
                m.id AS module_id,
                m.`name` AS module,
                m.`order`,
                0 AS show_operation,
                0 AS create_operation,
                0 AS update_operation,
                0 AS delete_operation,
                m.cud_operation
            FROM `module` AS m
            WHERE m.deleted_at IS NULL
            AND NOT EXISTS (
                SELECT 1
                FROM `permission` AS p
                WHERE p.deleted_at IS NULL
                AND p.role_id = :role_id
                AND p.module_id = m.id
            )

            ORDER BY `order` ASC;"
        );

        $this->db->bind(":role_id", $data["role_id"]);
        $row = $this->db->records();
        return $row;
    }

    public function insertPermission($data){
        $this->db->query(
            "INSERT INTO permission (role_id, module_id, show_operation, create_operation, update_operation, delete_operation, created_by, updated_by)
            VALUES (:role_id, :module_id, :show_operation, :create_operation, :update_operation, :delete_operation, :user_id, :user_id);"
        );

        $this->db->bind(":role_id", $data["role_id"]);
        $this->db->bind(":module_id", $data["module_id"]);
        $this->db->bind(":show_operation", $data["show_operation"]);
        $this->db->bind(":create_operation", $data["create_operation"]);
        $this->db->bind(":update_operation", $data["update_operation"]);
        $this->db->bind(":delete_operation", $data["delete_operation"]);
        $this->db->bind(":user_id", $data["user_id"]);
        if($this->db->execute()){
            return true;
        } else{
            return false;
        }
    }

    public function updatePermission($data){
        $this->db->query(
            "UPDATE permission
            SET role_id = :role_id,
            module_id = :module_id,
            show_operation = :show_operation,
            create_operation = :create_operation,
            update_operation = :update_operation,
            delete_operation = :delete_operation,
            updated_by = :user_id
            WHERE id = :id;"
        );
        
        $this->db->bind(":role_id", $data["role_id"]);
        $this->db->bind(":module_id", $data["module_id"]);
        $this->db->bind(":show_operation", $data["show_operation"]);
        $this->db->bind(":create_operation", $data["create_operation"]);
        $this->db->bind(":update_operation", $data["update_operation"]);
        $this->db->bind(":delete_operation", $data["delete_operation"]);
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