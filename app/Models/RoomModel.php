<?php
class RoomModel{
    private $db;

    public function __construct(){
        $this->db = new Database;
    }
    
    public function closeConnection() {
        $this->db->closeConnection();
    }

    public function getRooms(){
        $this->db->query(
            "SELECT r.id,
            	r.name,
            	b.id AS branch_id,
            	b.`name` AS branch_name,
            	r.capacity,
                r.`active`
            FROM room AS r
            LEFT JOIN branch AS b
            ON b.id = r.branch_id
            AND b.deleted_at IS NULL
            WHERE r.deleted_at IS NULL;"
        );

        $row = $this->db->records();
        return $row;
    }

    public function insertRoom($data){
        $this->db->query(
            "INSERT INTO room (name, branch_id, capacity, active, created_by, updated_by)
            VALUES (:name, :branch_id, :capacity, :active, :created_by, :updated_by);"
        );

        $this->db->bind(":name", $data["name"]);
        $this->db->bind(":branch_id", $data["branch_id"]);
        $this->db->bind(":capacity", $data["capacity"]);
        $this->db->bind(":active", $data["active"]);
        $this->db->bind(":created_by", $data["created_by"]);
        $this->db->bind(":updated_by", $data["updated_by"]);
        
        if($this->db->execute()){
            return true;
        } else{
            return false;
        }
    }

    public function updateRoom($data){
        $this->db->query(
            "UPDATE room
                SET name = :name,
                branch_id = :branch_id,
                capacity = :capacity,
                active = :active,
                updated_at = CURRENT_TIMESTAMP(),
                updated_by = :updated_by
                WHERE id = :id;"
        );

        $this->db->bind(":id", $data["id"]);
        $this->db->bind(":name", $data["name"]);
        $this->db->bind(":capacity", $data["capacity"]); 
        $this->db->bind(":branch_id", $data["branch_id"]);
        $this->db->bind(":active", $data["active"]);
        $this->db->bind(":updated_by", $data["updated_by"]);
        if($this->db->execute()){
            return true;
        } else{
            return false;
        }
    }

    public function deleteRoom($data){
        $this->db->query(
            "UPDATE room
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

    public function filterRoom($id){
        $this->db->query(
            "SELECT r.id,
            	r.name,
            	b.id AS branch_id,
            	b.`name` AS branch_name,
            	r.capacity,
                r.`active`
            FROM room AS r
            LEFT JOIN branch AS b
            ON b.id = r.branch_id
            AND b.active = 1
            WHERE r.id = :id
            AND r.deleted_at IS NULL;"
        );

        $this->db->bind(':id', $id);
        $row = $this->db->record();
        return $row;
    }
}
?>