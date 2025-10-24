<?php
class PatientModel{
    private $db;

    public function __construct(){
        $this->db = new Database;
    }
    
    public function closeConnection() {
        $this->db->closeConnection();
    }

    public function getPatients(){
        $this->db->query(
            "SELECT p.id,
                CONCAT(p.first_name, ' ', p.last_name) AS name,
                p.birth_date,
                p.gender,
                p.address,
                p.phone,
                p.email,
                IFNULL(pr.room, '') AS room,
                p.active
            FROM patient AS p
            LEFT JOIN (
            	SELECT ra.patient_id,
					IF(
						ra.`status` = 0,
						'',
						CONCAT(r.`name`, '-', b.`name`)
					) AS room
				FROM room_assignment AS ra
				INNER JOIN room AS r
				ON ra.room_id = r.id
				INNER JOIN branch AS b
				ON ra.branch_id = b.id
				WHERE ra.deleted_at IS NULL
			) AS pr
			ON p.id = pr.patient_id
            WHERE p.deleted_at IS NULL;"
        );

        $row = $this->db->records();
        return $row;
    }

    public function insertPatient($data){
        $this->db->query(
            "INSERT INTO patient (first_name, last_name, birth_date, gender, address, phone, email, active, created_by, updated_by)
             VALUES (:first_name, :last_name, :birth_date, :gender, :address, :phone, :email, :active, :created_by, :updated_by);"
        );

        $this->db->bind(":first_name", $data["first_name"]);
        $this->db->bind(":last_name", $data["last_name"]);
        $this->db->bind(":birth_date", $data["birth_date"]);
        $this->db->bind(":gender", $data["gender"]);
        $this->db->bind(":address", $data["address"]);
        $this->db->bind(":phone", $data["phone"]);
        $this->db->bind(":email", $data["email"]);
        $this->db->bind(":active", $data["active"]);
        $this->db->bind(":created_by", $data["created_by"]);
        $this->db->bind(":updated_by", $data["updated_by"]);
        
        if($this->db->execute()){
            return true;
        } else{
            return false;
        }
    }

    public function updatePatient($data){
        $this->db->query(
            "UPDATE patient
            SET first_name = :first_name,
            last_name = :last_name,
            birth_date = :birth_date,
            gender = :gender,
            address = :address,
            phone = :phone,
            email = :email,
            active = :active,
            updated_at = CURRENT_TIMESTAMP(),
            updated_by = :updated_by
            WHERE id = :id;"
        );

        $this->db->bind(":id", $data["id"]);
        $this->db->bind(":first_name", $data["first_name"]);
        $this->db->bind(":last_name", $data["last_name"]);
        $this->db->bind(":birth_date", $data["birth_date"]);
        $this->db->bind(":gender", $data["gender"]);
        $this->db->bind(":address", $data["address"]);
        $this->db->bind(":phone", $data["phone"]);
        $this->db->bind(":email", $data["email"]);
        $this->db->bind(":active", $data["active"]);
        $this->db->bind(":updated_by", $data["updated_by"]);
        if($this->db->execute()){
            return true;
        } else{
            return false;
        }
    }

    public function deletePatient($data){
        $this->db->query(
            "UPDATE patient
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

    public function filterPatient($id){
        $this->db->query(
            "SELECT p.id,
                p.first_name,
                p.last_name,
                p.birth_date,
                p.gender,
                p.address,
                p.phone,
                p.email,
                p.active
            FROM patient AS p
            WHERE p.id = :id
            AND p.deleted_at IS NULL;"
        );

        $this->db->bind(':id', $id);
        $row = $this->db->record();
        return $row;
    }
}
?>
