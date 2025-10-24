<?php
class EmployeeController extends Controllers {
    public function __construct() {
        parent::__construct();
    }
    
    public function index() {
        $this->view("EmployeeView");
    }

    public function show() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (!$this->authMiddleware->validateToken()) return;
            $employees = $this->model->getEmployees();
        
            if ($employees) {
                foreach ($employees as $employee){
                    $response[] = [
                        'id' => $employee->id,
                        'employee_name' => $employee->employee_name,
                        'phone' =>$employee->phone,
                        'email' => $employee->email,
                        'active' => $employee->active,
                        'position_id' =>$employee->position_id,
                        'position' =>$employee->position,
                        'branch_id' =>$employee->branch_id,
                        'branch' =>$employee->branch
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
                "first_name" => isset($decodedData['first_name']) ? htmlspecialchars($decodedData['first_name'], ENT_QUOTES, 'UTF-8') : null,
                "last_name" => isset($decodedData['last_name']) ? htmlspecialchars($decodedData['last_name'], ENT_QUOTES, 'UTF-8') : null,
                "phone" => isset($decodedData['phone']) ? htmlspecialchars($decodedData['phone'], ENT_QUOTES, 'UTF-8') : null,
                "email" => isset($decodedData['email']) ? filter_var($decodedData['email'], FILTER_SANITIZE_EMAIL) : null,
                "active" => isset($decodedData['active']) ? filter_var($decodedData['active'], FILTER_SANITIZE_NUMBER_INT) : null,
                "position_id" => isset($decodedData['position_id']) ? filter_var($decodedData['position_id'], FILTER_SANITIZE_NUMBER_INT) : null,
                "branch_id" => isset($decodedData['branch_id']) ? filter_var($decodedData['branch_id'], FILTER_SANITIZE_NUMBER_INT) : null,
                "created_by" => isset($decodedData['created_by']) ? filter_var($decodedData['created_by'], FILTER_SANITIZE_NUMBER_INT) : null,
                "updated_by" => isset($decodedData['updated_by']) ? filter_var($decodedData['updated_by'], FILTER_SANITIZE_NUMBER_INT) : null,
            ];
    
            if ($this->model->insertEmployee($data)) {
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
                "id" => isset($decodedData['id']) ? filter_var($decodedData['id'], FILTER_SANITIZE_NUMBER_INT) : null,
                "first_name" => isset($decodedData['first_name']) ? htmlspecialchars($decodedData['first_name'], ENT_QUOTES, 'UTF-8') : null,
                "last_name" => isset($decodedData['last_name']) ? htmlspecialchars($decodedData['last_name'], ENT_QUOTES, 'UTF-8') : null,
                "phone" => isset($decodedData['phone']) ? htmlspecialchars($decodedData['phone'], ENT_QUOTES, 'UTF-8') : null,
                "email" => isset($decodedData['email']) ? filter_var($decodedData['email'], FILTER_SANITIZE_EMAIL) : null,
                "position_id" => isset($decodedData['position_id']) ? filter_var($decodedData['position_id'], FILTER_SANITIZE_NUMBER_INT) : null,
                "branch_id" => isset($decodedData['branch_id']) ? filter_var($decodedData['branch_id'], FILTER_SANITIZE_NUMBER_INT) : null,
                "active" => isset($decodedData['active']) ? filter_var($decodedData['active'], FILTER_SANITIZE_NUMBER_INT) : null,
                "updated_by" => isset($decodedData['updated_by']) ? filter_var($decodedData['updated_by'], FILTER_SANITIZE_NUMBER_INT) : null
            ];
            
            if ($this->model->updateEmployee($data)) {
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
    
    
            if ($this->model->deleteEmployee($data)) {
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
    
            $id = isset($decodedData['id']) ? filter_var($decodedData['id'], FILTER_SANITIZE_NUMBER_INT) : null;
            $employee = $this->model->filterEmployee($id);
    
            if ($employee) {
                $response = [
                    'id' => $employee->id,
                    'first_name' => $employee->first_name,
                    'last_name' => $employee->last_name,
                    'phone' => $employee->phone,
                    'email' => $employee->email,
                    'active' =>$employee->active,
                    'position_id' =>$employee->position_id,
                    'position' =>$employee->position,
                    'branch_id' =>$employee->branch_id,
                    'branch' =>$employee->branch
                ];
                
                $this->jsonResponse($response);                
            }
        }
    }
}
?>
