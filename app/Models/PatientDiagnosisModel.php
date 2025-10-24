<?php
class PatientDiagnosisModel{
    private $db;

    public function __construct(){
        $this->db = new Database;
    }
    
    public function closeConnection() {
        $this->db->closeConnection();
    }

    public function getPatientDiagnoses(){
        $this->db->query(
            "SELECT pd.id,
            	pd.patient_id,
            	CONCAT(p.first_name, ' ', p.last_name) AS patient_name,
            	pd.employee_id,
            	CONCAT(e.first_name, ' ', e.last_name) AS employee_name,
            	pd.branch_id,
            	b.`name` AS branch_name,
            	pd.visit_date,
            	pd.diagnosis,
            	pde.exams_id,
            	pde.exams,
            	pd.treatment_plan
            FROM patient_diagnoses AS pd
            LEFT JOIN patient AS p
            ON pd.patient_id = p.id
            AND p.deleted_at IS NULL
            LEFT JOIN employee AS e
            ON pd.employee_id = e.id
            AND e.deleted_at IS NULL
            LEFT JOIN branch AS b
            ON pd.branch_id = b.id
            AND b.deleted_at IS NULL
            LEFT JOIN (
            	SELECT patient_diagnoses_id,
            		GROUP_CONCAT(e.id) AS exams_id,
            		GROUP_CONCAT(e.name) AS exams
            	FROM patient_exams AS pe
            	LEFT JOIN exam AS e
            	ON pe.exam_id = e.id
            	AND e.deleted_at IS NULL
            	WHERE pe.deleted_at IS NULL
                AND pe.assigned = 1
            	GROUP BY patient_diagnoses_id
			) AS pde
			ON pd.id = patient_diagnoses_id
            WHERE pd.deleted_at IS NULL;"
        );

        $row = $this->db->records();
        return $row;
    }

    public function insertPatientDiagnosis($data){
        $this->db->query(
            "INSERT INTO patient_diagnoses
             (patient_id,
			  employee_id,
			  branch_id,
              visit_date,
              diagnosis,
              treatment_plan,
              created_by,
              updated_by
              )
             VALUES
             (
              :patient_id,
              :employee_id,
              :branch_id,
              :visit_date,
              :diagnosis,
              :treatment_plan,
			  :created_by,
			  :updated_by);"
        );

        $this->db->bind(":patient_id", $data["patient_id"]);
        $this->db->bind(":employee_id", $data["employee_id"]);
        $this->db->bind(":branch_id", $data["branch_id"]);
        $this->db->bind(":visit_date", $data["visit_date"]);
        $this->db->bind(":diagnosis", $data["diagnosis"]);
        $this->db->bind(":treatment_plan", $data["treatment_plan"]);
        $this->db->bind(":created_by", $data["created_by"]);
        $this->db->bind(":updated_by", $data["updated_by"]);
        
        if($this->db->execute()){
            return $this->db->lastInsertId();
        } else{
            return false;
        }
    }

    public function updatePatientDiagnosis($data){
        $this->db->query(
            "UPDATE patient_diagnoses
                    SET patient_id = :patient_id,
                    employee_id = :employee_id,
                    branch_id = :branch_id,
                    visit_date = :visit_date,
                    diagnosis = :diagnosis,
                    treatment_plan = :treatment_plan,
                    updated_at = CURRENT_TIMESTAMP(),
                    updated_by = :updated_by
                    WHERE id = :id;"
        );

        $this->db->bind(":id", $data["id"]);
        $this->db->bind(":patient_id", $data["patient_id"]);
        $this->db->bind(":employee_id", $data["employee_id"]);
        $this->db->bind(":branch_id", $data["branch_id"]);
        $this->db->bind(":visit_date", $data["visit_date"]);
        $this->db->bind(":diagnosis", $data["diagnosis"]);
        $this->db->bind(":treatment_plan", $data["treatment_plan"]);
        $this->db->bind(":updated_by", $data["updated_by"]);
        if($this->db->execute()){
            return true;
        } else{
            return false;
        }
    }

    public function deletePatientDiagnosis($data){
        $this->db->query(
            "UPDATE patient_diagnoses
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

    public function filterPatientDiagnosis($id){
        $this->db->query(
            "SELECT pd.id,
            	pd.patient_id,
            	CONCAT(p.first_name, ' ', p.last_name) AS patient_name,
            	pd.employee_id,
            	CONCAT(e.first_name, ' ', e.last_name) AS employee_name,
            	pd.branch_id,
            	b.`name` AS branch_name,
            	pd.visit_date,
            	pd.diagnosis,
            	pde.exams_id,
            	pde.exams,
            	pd.treatment_plan
            FROM patient_diagnoses AS pd
            LEFT JOIN patient AS p
            ON pd.patient_id = p.id
            AND p.deleted_at IS NULL
            LEFT JOIN employee AS e
            ON pd.employee_id = e.id
            AND e.deleted_at IS NULL
            LEFT JOIN branch AS b
            ON pd.branch_id = b.id
            AND b.deleted_at IS NULL
            LEFT JOIN (
            	SELECT patient_diagnoses_id,
            		GROUP_CONCAT(e.id) AS exams_id,
            		GROUP_CONCAT(e.name) AS exams
            	FROM patient_exams AS pe
            	LEFT JOIN exam AS e
            	ON pe.exam_id = e.id
            	AND e.deleted_at IS NULL
            	WHERE pe.deleted_at IS NULL
                AND pe.assigned = 1
            	GROUP BY patient_diagnoses_id
			) AS pde
			ON pd.id = patient_diagnoses_id
            WHERE pd.deleted_at IS NULL
            AND pd.id = :id;"
        );

        $this->db->bind(':id', $id);

        $row = $this->db->record();
        return $row;
    }
}
?>