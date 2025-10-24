<?php
class BranchController extends Controllers {
    public function __construct() {
        parent::__construct();
    }
    
    public function index() {
        $this->view("BranchView");
    }

    public function show() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (!$this->authMiddleware->validateToken()) return;
            $branches = $this->model->getBranches();
        
            if ($branches) {
                foreach ($branches as $branch){
                    $response[] = [
                        'id' => $branch->id,
                        'name' => $branch->name,
                        'address' =>$branch->address,
                        'phone' =>$branch->phone,
                        'city' =>$branch->city,
                        'active' =>$branch->active
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
                "name" => isset($decodedData['name']) ? htmlspecialchars($decodedData['name'], ENT_QUOTES, 'UTF-8') : null,
                "address" => isset($decodedData['address']) ? htmlspecialchars($decodedData['address'], ENT_QUOTES, 'UTF-8') : null,
                "phone" => isset($decodedData['phone']) ? htmlspecialchars($decodedData['phone'], ENT_QUOTES, 'UTF-8') : null,
                "city" => isset($decodedData['city']) ? htmlspecialchars($decodedData['city'], ENT_QUOTES, 'UTF-8') : null,
                "active" => isset($decodedData['active']) ? filter_var($decodedData['active'], FILTER_SANITIZE_NUMBER_INT) : null,
                "created_by" => isset($decodedData['created_by']) ? filter_var($decodedData['created_by'], FILTER_SANITIZE_NUMBER_INT) : null,
                "updated_by" => isset($decodedData['updated_by']) ? filter_var($decodedData['updated_by'], FILTER_SANITIZE_NUMBER_INT) : null
            ];
    
            if ($this->model->insertBranch($data)) {
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
                "name" => isset($decodedData['name']) ? htmlspecialchars($decodedData['name'], ENT_QUOTES, 'UTF-8') : null,
                "address" => isset($decodedData['address']) ? htmlspecialchars($decodedData['address'], ENT_QUOTES, 'UTF-8') : null,
                "phone" => isset($decodedData['phone']) ? htmlspecialchars($decodedData['phone'], ENT_QUOTES, 'UTF-8') : null,
                "city" => isset($decodedData['city']) ? htmlspecialchars($decodedData['city'], ENT_QUOTES, 'UTF-8') : null,
                "active" => isset($decodedData['active']) ? filter_var($decodedData['active'], FILTER_SANITIZE_NUMBER_INT) : null,
                "updated_by" => isset($decodedData['updated_by']) ? filter_var($decodedData['updated_by'], FILTER_SANITIZE_NUMBER_INT) : null
            ];
            
            if ($this->model->updateBranch($data)) {
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
    
            if ($this->model->deleteBranch($data)) {
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
            $branch = $this->model->filterBranch($id);
    
            if ($branch) {
                $response = [
                    'id' => $branch->id,
                    'name' => $branch->name,
                    'address' =>$branch->address,
                    'phone' =>$branch->phone,
                    'city' =>$branch->city,
                    'active' =>$branch->active
                ];
            
                $this->jsonResponse($response);
            }
        }
    }
}
?>