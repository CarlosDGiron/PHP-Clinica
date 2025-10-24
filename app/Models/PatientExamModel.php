<?php
class PatientExamModel{
    private $db;

    public function __construct(){
        $this->db = new Database;
    }
    
    public function closeConnection() {
        $this->db->closeConnection();
    }

    public function insertPatientExam($data){
        $this->db->query(
            "INSERT INTO patient_exams (patient_diagnoses_id, exam_id, assigned)
             VALUES (:patient_diagnoses_id, :exam_id, :assigned);"
        );

        $this->db->bind(":patient_diagnoses_id", $data["patient_diagnoses_id"]);
        $this->db->bind(":exam_id", $data["exam_id"]);
        $this->db->bind(":assigned", $data["assigned"]);
        
        if($this->db->execute()){
            return true;
        } else{
            return false;
        }
    }

    public function updatePatientExam($data){
        $this->db->query(
            "UPDATE patient_exams
            SET patient_diagnoses_id = :patient_diagnoses_id,
            exam_id = :exam_id,
            assigned = :assigned,
            updated_at = CURRENT_TIMESTAMP(),
            updated_by = :updated_by
            WHERE patient_diagnoses_id = :patient_diagnoses_id
            AND exam_id = :exam_id;"
        );

        $this->db->bind(":patient_diagnoses_id", $data["patient_diagnoses_id"]);
        $this->db->bind(":exam_id", $data["exam_id"]);
        $this->db->bind(":assigned", $data["assigned"]);
        $this->db->bind(":updated_by", $data["updated_by"]);
        if($this->db->execute()){
            return true;
        } else{
            return false;
        }
    }

    public function deletePatientExam($data){
        $this->db->query(
            "UPDATE patient_exams
            SET deleted_at = CURRENT_TIMESTAMP(),
            deleted_by = :deleted_by
            WHERE patient_diagnoses_id = :patient_diagnoses_id;"
        );

        $this->db->bind(":patient_diagnoses_id", $data["patient_diagnoses_id"]);
        $this->db->bind(":deleted_by", $data["deleted_by"]);

        if($this->db->execute()){
            return true;
        } else{
            return false;
        }
    }

    public function filterPatientExam($data){
        $this->db->query(
            "SELECT pe.id,
                pe.assigned
            FROM patient_exams AS pe
            WHERE pe.deleted_at IS NULL
            AND pe.patient_diagnoses_id = :patient_diagnoses_id
            AND pe.exam_id = :exam_id;"
        );

        $this->db->bind(":patient_diagnoses_id", $data["patient_diagnoses_id"]);
        $this->db->bind(":exam_id", $data["exam_id"]);

        $row = $this->db->record();
        return $row;
    }
}
?>