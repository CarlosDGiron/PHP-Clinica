<?php
class EmployeeModel{
    private $db;

    public function __construct(){
        $this->db = new Database;
    }
    
    public function closeConnection() {
        $this->db->closeConnection();
    }

    public function getEmployees(){
        $this->db->query(
            "SELECT 
                e.id,
                CONCAT(e.first_name, ' ', e.last_name) AS employee_name,
                e.phone,
                e.email,
                e.active,
                p.id AS position_id,
                p.name AS position,
                b.id AS branch_id,
                b.name AS branch
            FROM employee AS e
            LEFT JOIN position AS p
            ON p.id = e.position_id
            AND p.deleted_at IS NULL
            LEFT JOIN branch AS b
            ON b.id = e.branch_id
            AND b.deleted_at IS NULL
            WHERE e.deleted_at IS NULL;"
        );

        $row = $this->db->records();
        return $row;
    }

    public function insertEmployee($data){
        $this->db->query(
            "INSERT INTO employee (first_name, last_name, phone, email, `active`, position_id, branch_id, created_by, updated_by)
            VALUES (:first_name, :last_name, :phone, :email, :active, :position_id, :branch_id, :created_by, :updated_by);"
        );

        $this->db->bind(":first_name", $data["first_name"]);
        $this->db->bind(":last_name", $data["last_name"]);
        $this->db->bind(":phone", $data["phone"]);
        $this->db->bind(":email", $data["email"]);
        $this->db->bind(":active", $data["active"]);
        $this->db->bind(":position_id", $data["position_id"]);
        $this->db->bind(":branch_id", $data["branch_id"]);
        $this->db->bind(":created_by", $data["created_by"]);
        $this->db->bind(":updated_by", $data["updated_by"]);
        
        if($this->db->execute()){
            return true;
        } else{
            return false;
        }
    }

    public function updateEmployee($data){
        $this->db->query(
            "UPDATE employee
            SET first_name = :first_name,
            last_name = :last_name,
            phone = :phone,
            email = :email,
            position_id = :position_id,
            branch_id = :branch_id,
            `active` = :active,
            updated_by = :updated_by
            WHERE id = :id;"
        );

        $this->db->bind(":id", $data["id"]);
        $this->db->bind(":first_name", $data["first_name"]);
        $this->db->bind(":last_name", $data["last_name"]);
        $this->db->bind(":phone", $data["phone"]);
        $this->db->bind(":email", $data["email"]);
        $this->db->bind(":position_id", $data["position_id"]);
        $this->db->bind(":branch_id", $data["branch_id"]);
        $this->db->bind(":active", $data["active"]);
        $this->db->bind(":updated_by", $data["updated_by"]);
        if($this->db->execute()){
            return true;
        } else{
            return false;
        }
    }

    public function deleteEmployee($data){
        $this->db->query(
            "UPDATE employee
            SET deleted_at = CURRENT_TIMESTAMP(),
            deleted_by = :deleted_by
            WHERE id = :id;"
        );

        $this->db->bind(":id", $data["id"]);
        $this->db->bind(":deleted_by", $data["deleted_by"]);

        if($this->db->execute()){
            return true;
        } else{
            return false;
        }
    }

    public function filterEmployee($id){
        $this->db->query(
            "SELECT e.id,
                e.first_name,
                e.last_name,
                e.phone,
                e.email,
                e.active,
                p.id AS position_id,
                p.name AS position,
                b.id AS branch_id,
                b.name AS branch
            FROM employee AS e
            LEFT JOIN position AS p
            ON p.id = e.position_id
            AND p.active = 1
            AND p.deleted_at IS NULL
            LEFT JOIN branch AS b
            ON b.id = e.branch_id
            AND b.active = 1
            AND b.deleted_at IS NULL
            WHERE e.id = :id
            AND e.deleted_at IS NULL;"
        );

        $this->db->bind(':id', $id);

        $row = $this->db->record();
        return $row;
    }
}
?>
