<?php
class SupplierModel{
    private $db;

    public function __construct(){
        $this->db = new Database;
    }
    
    public function closeConnection() {
        $this->db->closeConnection();
    }

    public function getSuppliers(){
        $this->db->query(
            "SELECT s.id,
                s.name,
                s.description,
                s.email,
                s.phone,
                s.address,
                s.active
            FROM supplier AS s
            WHERE s.deleted_at IS NULL;"
        );

        $row = $this->db->records();
        return $row;
    }

    public function insertSupplier($data){
        $this->db->query(
            "INSERT INTO supplier 
             (`name`, 
              description,
              email,
              phone,
			  address,
			  active,
              created_by,
              updated_by)
             VALUES
             (:name, 
              :description,
              :email,
              :phone,
              :address,
              :active,
			  :created_by,
			  :updated_by);"
        );

        $this->db->bind(":name", $data["name"]);
        $this->db->bind(":description", $data["description"]);
        $this->db->bind(":email", $data["email"]);
        $this->db->bind(":phone", $data["phone"]);
        $this->db->bind(":address", $data["address"]);
        $this->db->bind(":active", $data["active"]);
        $this->db->bind(":created_by", $data["created_by"]);
        $this->db->bind(":updated_by", $data["updated_by"]);
        
        if($this->db->execute()){
            return true;
        } else{
            return false;
        }
    }

    public function updateSupplier($data){
        $this->db->query(
            "UPDATE supplier
            SET `name` = :name,
            description = :description,
            email = :email,
            phone = :phone,
            address = :address,
            active = :active,
            updated_at = CURRENT_TIMESTAMP(),
            updated_by = :updated_by
            WHERE id = :id;"
        );

        $this->db->bind(":id", $data["id"]);
        $this->db->bind(":name", $data["name"]);
        $this->db->bind(":description", $data["description"]);
        $this->db->bind(":email", $data["email"]);
        $this->db->bind(":phone", $data["phone"]);
        $this->db->bind(":address", $data["address"]);
        $this->db->bind(":active", $data["active"]);
        $this->db->bind(":updated_by", $data["updated_by"]);
        if($this->db->execute()){
            return true;
        } else{
            return false;
        }
    }

    public function deleteSupplier($data){
        $this->db->query(
            "UPDATE supplier
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

    public function filterSupplier($id){
        $this->db->query(
            "SELECT s.id,
                s.name,
                s.description,
                s.email,
                s.phone,
                s.address,
                s.active
            FROM supplier AS s
            WHERE s.id = :id
            AND s.deleted_at IS NULL;");

        $this->db->bind(':id', $id);

        $row = $this->db->record();
        return $row;
    }
}
?>
