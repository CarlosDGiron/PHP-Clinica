<?php
class RoomAssignmentModel{
    private $db;

    public function __construct(){
        $this->db = new Database;
    }
    
    public function closeConnection() {
        $this->db->closeConnection();
    }

    public function getRooms($data){
        $this->db->query(
            "SELECT r.id,
                r.`name`
            FROM room r
            WHERE r.deleted_at IS NULL
            AND r.branch_id = :branch_id
            AND r.capacity > (
                SELECT COUNT(*)
                FROM room_assignment AS ra
                WHERE ra.deleted_at IS NULL
                AND ra.room_id = r.id
                AND ra.branch_id = r.branch_id
                AND ra.patient_id != :patient_id
            );"
        );

        $this->db->bind(":branch_id", $data["branch_id"]);
        $this->db->bind(":patient_id", $data["patient_id"]);
        $row = $this->db->records();
        return $row;
    }

    public function insertRoomAssignment($data){
        $this->db->query(
            "INSERT INTO room_assignment
             (patient_id,
			  room_id,
			  branch_id,
			  status,
              created_by,
              updated_by)
             VALUES
             (
              :patient_id,
              :room_id,
              :branch_id,
              :status,
			  :created_by,
			  :updated_by);"
        );

        $this->db->bind(":patient_id", $data["patient_id"]);
        $this->db->bind(":room_id", $data["room_id"]);
        $this->db->bind(":branch_id", $data["branch_id"]);
        $this->db->bind(":status", $data["status"]);
        $this->db->bind(":created_by", $data["created_by"]);
        $this->db->bind(":updated_by", $data["updated_by"]);
        
        if($this->db->execute()){
            return true;
        } else{
            return false;
        }
    }

    public function updateRoomAssignment($data){
        $this->db->query(
            "UPDATE room_assignment
                SET room_id = :room_id,
                branch_id = :branch_id,
                status = :status,
                updated_at = CURRENT_TIMESTAMP(),
                updated_by = :updated_by
                WHERE patient_id = :patient_id;"
        );

        $this->db->bind(":patient_id", $data["patient_id"]);
        $this->db->bind(":room_id", $data["room_id"]);
        $this->db->bind(":branch_id", $data["branch_id"]);
        $this->db->bind(":status", $data["status"]);
        $this->db->bind(":updated_by", $data["updated_by"]);
        if($this->db->execute()){
            return true;
        } else{
            return false;
        }
    }

    public function filterRoomAssignment($patient_id) {
        $this->db->query(
            "SELECT ra.patient_id,
                r.`name` AS room_name,
                ra.room_id,
                b.`name` AS branch_name,
                ra.branch_id,
                ra.status
            FROM room_assignment AS ra
            INNER JOIN room AS r
            ON ra.room_id = r.id
            INNER JOIN branch AS b
            ON ra.branch_id = b.id
            WHERE ra.deleted_at IS NULL
            AND ra.status = 1
            AND ra.patient_id = :patient_id;"
        );

        $this->db->bind(':patient_id', $patient_id);

        $row = $this->db->record();
        return $row;
    }

    public function searchRoomAssignment($patient_id) {
        $this->db->query(
            "SELECT ra.patient_id,
                ra.room_id,
                ra.branch_id,
                ra.status
            FROM room_assignment AS ra
            WHERE ra.deleted_at IS NULL
            AND ra.patient_id = :patient_id;"
        );

        $this->db->bind(':patient_id', $patient_id);

        $row = $this->db->record();
        return $row;
    }
}
?>