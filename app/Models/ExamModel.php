<?php
class ExamModel{
    private $db;

    public function __construct(){
        $this->db = new Database;
    }
    
    public function closeConnection() {
        $this->db->closeConnection();
    }

    public function getExams(){
        $this->db->query(
            "SELECT e.id,
                e.name,
                e.description,
                e.active
            FROM exam AS e
            WHERE e.deleted_at IS NULL;"
        );

        $row = $this->db->records();
        return $row;
    }

    public function insertExam($data){
        $this->db->query(
            "INSERT INTO exam (name,  description,  active, created_by, updated_by)
            VALUES (:name, :description, :active, :created_by, :updated_by);"
        );

        $this->db->bind(":name", $data["name"]);
        $this->db->bind(":description", $data["description"]);
        $this->db->bind(":active", $data["active"]);
        $this->db->bind(":created_by", $data["created_by"]);
        $this->db->bind(":updated_by", $data["updated_by"]);
        
        if($this->db->execute()){
            return true;
        } else{
            return false;
        }
    }

    public function updateExam($data){
        $this->db->query(
            "UPDATE exam
            SET name = :name,
            description = :description,
            active = :active,
            updated_at = CURRENT_TIMESTAMP(),
            updated_by = :updated_by
            WHERE id = :id;"
        );

        $this->db->bind(":id", $data["id"]);
        $this->db->bind(":name", $data["name"]);
        $this->db->bind(":description", $data["description"]);
        $this->db->bind(":active", $data["active"]);
        $this->db->bind(":updated_by", $data["updated_by"]);
        if($this->db->execute()){
            return true;
        } else{
            return false;
        }
    }

    public function deleteExam($data){
        $this->db->query(
            "UPDATE exam
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

    public function filterExam($id){
        $this->db->query(
            "SELECT e.id,
                    e.name,
                    e.description,
                    e.active
                FROM exam AS e
                WHERE e.id = :id
                AND e.deleted_at IS NULL;");

        $this->db->bind(':id', $id);
        $row = $this->db->record();
        return $row;
    }
}
?>