<?php
class PatientDiagnosisController extends Controllers {
    public function __construct() {
        parent::__construct();
    }
    
    public function index() {
        $this->view("PatientDiagnosisView");
    }

    public function show() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (!$this->authMiddleware->validateToken()) return;
            $patientDiagnoses = $this->model->getPatientDiagnoses();
        
            if ($patientDiagnoses) {
                foreach ($patientDiagnoses as $patientDiagnosis){
                    $response[] = [
                        'id' => $patientDiagnosis->id,
                        'patient_id' =>$patientDiagnosis->patient_id,
                        'patient_name' =>$patientDiagnosis->patient_name,
                        'employee_id' =>$patientDiagnosis->employee_id,
                        'employee_name' =>$patientDiagnosis->employee_name,
                        'branch_id' =>$patientDiagnosis->branch_id,
                        'branch_name' => $patientDiagnosis->branch_name,
                        'visit_date' => $patientDiagnosis->visit_date,
                        'diagnosis' => $patientDiagnosis->diagnosis,
                        'exams_id' => $patientDiagnosis->exams_id,
                        'exams' => $patientDiagnosis->exams,
                        'treatment_plan' => $patientDiagnosis->treatment_plan
                    ];
                }

                $this->jsonResponse($response);
            }
        }
    }

    public function insert() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->authMiddleware->validateToken()) return;
            
            $json = file_get_contents('php://input');
            $decodedData = json_decode($json, true); 
    
            $data = [
                "patient_id" => isset($decodedData['patient_id']) ? filter_var($decodedData['patient_id'], FILTER_SANITIZE_NUMBER_INT) : null,
                "employee_id" => isset($decodedData['employee_id']) ? filter_var($decodedData['employee_id'], FILTER_SANITIZE_NUMBER_INT) : null,
                "branch_id" => isset($decodedData['branch_id']) ? filter_var($decodedData['branch_id'], FILTER_SANITIZE_NUMBER_INT) : null,
                "visit_date" => isset($decodedData['visit_date']) ? htmlspecialchars($decodedData['visit_date'], ENT_QUOTES, 'UTF-8') : null,
                "diagnosis" => isset($decodedData['diagnosis']) ? htmlspecialchars($decodedData['diagnosis'], ENT_QUOTES, 'UTF-8') : null,
                "treatment_plan" => isset($decodedData['treatment_plan']) ? htmlspecialchars($decodedData['treatment_plan'], ENT_QUOTES, 'UTF-8') : null,
                "created_by" => isset($decodedData['created_by']) ? filter_var($decodedData['created_by'], FILTER_SANITIZE_NUMBER_INT) : null,
                "updated_by" => isset($decodedData['updated_by']) ? filter_var($decodedData['updated_by'], FILTER_SANITIZE_NUMBER_INT) : null
            ];

            $patientDiagnosisId = $this->model->insertPatientDiagnosis($data);
    
            if ($patientDiagnosisId) {
                $this->jsonResponse(["success" => true, "diagnosis_id" => $patientDiagnosisId]);
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
                "id" => isset($decodedData['id']) ? filter_var($decodedData['id'], FILTER_SANITIZE_NUMBER_INT) : null,
                "patient_id" => isset($decodedData['patient_id']) ? filter_var($decodedData['patient_id'], FILTER_SANITIZE_NUMBER_INT) : null,
                "employee_id" => isset($decodedData['employee_id']) ? filter_var($decodedData['employee_id'], FILTER_SANITIZE_NUMBER_INT) : null,
                "branch_id" => isset($decodedData['branch_id']) ? filter_var($decodedData['branch_id'], FILTER_SANITIZE_NUMBER_INT) : null,
                "visit_date" => isset($decodedData['visit_date']) ? htmlspecialchars($decodedData['visit_date'], ENT_QUOTES, 'UTF-8') : null,
                "diagnosis" => isset($decodedData['diagnosis']) ? htmlspecialchars($decodedData['diagnosis'], ENT_QUOTES, 'UTF-8') : null,
                "treatment_plan" => isset($decodedData['treatment_plan']) ? htmlspecialchars($decodedData['treatment_plan'], ENT_QUOTES, 'UTF-8') : null,
                "updated_by" => isset($decodedData['updated_by']) ? filter_var($decodedData['updated_by'], FILTER_SANITIZE_NUMBER_INT) : null
            ];
            
            if ($this->model->updatePatientDiagnosis($data)) {
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
                "id" => isset($decodedData['id']) ? filter_var($decodedData['id'], FILTER_SANITIZE_NUMBER_INT) : null, 
                "deleted_by" => isset($decodedData['deleted_by']) ? filter_var($decodedData['deleted_by'], FILTER_SANITIZE_NUMBER_INT) : null 
            ];
    
    
            if ($this->model->deletePatientDiagnosis($data)) {
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
    
            $id = isset($decodedData['id']) ? filter_var($decodedData['id'], FILTER_SANITIZE_SPECIAL_CHARS) : null;
            $patientDiagnosis = $this->model->filterPatientDiagnosis($id);
    
            if ($patientDiagnosis) {
                $response = [
                    'id' => $patientDiagnosis->id,
                    'patient_id' =>$patientDiagnosis->patient_id,
                    'patient_name' =>$patientDiagnosis->patient_name,
                    'employee_id' =>$patientDiagnosis->employee_id,
                    'employee_name' =>$patientDiagnosis->employee_name,
                    'branch_id' =>$patientDiagnosis->branch_id,
                    'branch_name' => $patientDiagnosis->branch_name,
                    'visit_date' => $patientDiagnosis->visit_date,
                    'diagnosis' => $patientDiagnosis->diagnosis,
                    'exams_id' => $patientDiagnosis->exams_id,
                    'exams' => $patientDiagnosis->exams,
                    'treatment_plan' => $patientDiagnosis->treatment_plan
                ];

                $this->jsonResponse($response);
            }
        }
    }
}
?>