<?php
class BranchModel{
    private $db;

    public function __construct(){
        $this->db = new Database;
    }
    
    public function closeConnection() {
        $this->db->closeConnection();
    }

    public function getBranches(){
        $this->db->query(
            "SELECT b.id,
                b.`name`,
                b.address,
                b.phone,
                b.city,
                b.active
            FROM branch b
            WHERE b.deleted_at IS NULL;"
        );

        $row = $this->db->records();
        return $row;
    }

    public function insertBranch($data){
        $this->db->query(
            "INSERT INTO branch
             (`name`, 
              `address`,
			  phone,
			  city,
			  active,
              created_by,
              updated_by)
             VALUES
             (:name, 
              :address,
              :phone,
              :city,
              :active,
			  :created_by,
			  :updated_by);"
        );

        $this->db->bind(":name", $data["name"]);
        $this->db->bind(":address", $data["address"]);
        $this->db->bind(":phone", $data["phone"]);
        $this->db->bind(":city", $data["city"]);
        $this->db->bind(":active", $data["active"]);
        $this->db->bind(":created_by", $data["created_by"]);
        $this->db->bind(":updated_by", $data["updated_by"]);
        
        if($this->db->execute()){
            return true;
        } else{
            return false;
        }
    }

    public function updateBranch($data){
        $this->db->query(
            "UPDATE branch
                    SET `name` = :name,
                    `address` = :address,
                    phone = :phone,
                    city = :city,
                    active = :active,
                    updated_at = CURRENT_TIMESTAMP(),
                    updated_by = :updated_by
                    WHERE id = :id;"
        );

        $this->db->bind(":id", $data["id"]);
        $this->db->bind(":name", $data["name"]);
        $this->db->bind(":address", $data["address"]);
        $this->db->bind(":phone", $data["phone"]);
        $this->db->bind(":city", $data["city"]);
        $this->db->bind(":active", $data["active"]);
        $this->db->bind(":updated_by", $data["updated_by"]);
        if($this->db->execute()){
            return true;
        } else{
            return false;
        }
    }

    public function deleteBranch($data){
        $this->db->query(
            "UPDATE branch
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

    public function filterBranch($id){
        $this->db->query(
            "SELECT 
                b.id,
                b.`name`,
                b.address,
                b.phone,
                b.city,
                b.active
            FROM branch b
            WHERE b.id = :id
            AND b.deleted_at IS NULL;");

        $this->db->bind(':id', $id);

        $row = $this->db->record();
        return $row;
    }
}
?>