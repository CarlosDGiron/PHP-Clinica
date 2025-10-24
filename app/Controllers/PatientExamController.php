<?php
class PatientExamController extends Controllers {
    public function __construct() {
        parent::__construct();
    }
    
    public function index() {}

    public function insert() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->authMiddleware->validateToken()) return;
            
            $json = file_get_contents('php://input');
            $decodedData = json_decode($json, true); 
    
            $data = [
                "patient_diagnoses_id" => isset($decodedData['patient_diagnoses_id']) ? filter_var($decodedData['patient_diagnoses_id'], filter: FILTER_SANITIZE_NUMBER_INT) : null,
                "exam_id" => isset($decodedData['exam_id']) ? filter_var($decodedData['exam_id'], FILTER_SANITIZE_NUMBER_INT) : null,
                "assigned" => isset($decodedData['assigned']) ? filter_var($decodedData['assigned'], FILTER_SANITIZE_NUMBER_INT) : null,
                "created_by" => isset($decodedData['created_by']) ? filter_var($decodedData['created_by'], FILTER_SANITIZE_NUMBER_INT) : null,
                "updated_by" => isset($decodedData['updated_by']) ? filter_var($decodedData['updated_by'], FILTER_SANITIZE_NUMBER_INT) : null
            ];
    
            if ($this->model->insertPatientExam($data)) {
                $this->jsonResponse(["success" => true]);
            } else {
                $this->jsonResponse(["success" => false]);
            }
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->authMiddleware->validateToken()) return;
            
            // Obtener el contenido de la solicitud y decodificar el JSON
            $json = file_get_contents('php://input');
            $decodedData = json_decode($json, true); // Decodifica el JSON en un array asociativo
    
            $data = [
                "patient_diagnoses_id" => isset($decodedData['patient_diagnoses_id']) ? filter_var($decodedData['patient_diagnoses_id'], FILTER_SANITIZE_NUMBER_INT) : null,
                "exam_id" => isset($decodedData['exam_id']) ? filter_var($decodedData['exam_id'], FILTER_SANITIZE_NUMBER_INT) : null,
                "assigned" => isset($decodedData['assigned']) ? filter_var($decodedData['assigned'], FILTER_SANITIZE_NUMBER_INT) : null,
                "updated_by" => isset($decodedData['updated_by']) ? filter_var($decodedData['updated_by'], FILTER_SANITIZE_NUMBER_INT) : null
            ];
            
            if ($this->model->updatePatientExam($data)) {
                $this->jsonResponse(["success" => true]);
            } else {
                $this->jsonResponse(["success" => false]);
            }
        }
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->authMiddleware->validateToken()) return;
    
            // Obtener el contenido de la solicitud y decodificar el JSON
            $json = file_get_contents('php://input');
            $decodedData = json_decode($json, true); // Decodifica el JSON en un array asociativo
    
            $data = [
                "patient_diagnoses_id" => isset($decodedData['patient_diagnoses_id']) ? filter_var($decodedData['patient_diagnoses_id'], FILTER_SANITIZE_NUMBER_INT) : null, 
                "deleted_by" => isset($decodedData['deleted_by']) ? filter_var($decodedData['deleted_by'], FILTER_SANITIZE_NUMBER_INT) : null 
            ];
    
    
            if ($this->model->deletePatientExam($data)) {
                $this->jsonResponse(["success" => true]);
            } else {
                $this->jsonResponse(["success" => false]);
            }
        }
    }

    public function filter() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->authMiddleware->validateToken()) return;
            $json = file_get_contents('php://input');
            $decodedData = json_decode($json, true); 
    
            $data = [
                "patient_diagnoses_id" => isset($decodedData['patient_diagnoses_id']) ? filter_var($decodedData['patient_diagnoses_id'], FILTER_SANITIZE_NUMBER_INT) : null,
                "exam_id" => isset($decodedData['exam_id']) ? filter_var($decodedData['exam_id'], FILTER_SANITIZE_NUMBER_INT) : null
            ];
            
            $patientExam = $this->model->filterPatientExam($data);
    
            if ($patientExam) {
                $response = [
                    'id' => $patientExam->id,
                    'assigned' =>$patientExam->assigned
                ];

                $this->jsonResponse($response);
            }
        }
    }
}
?>