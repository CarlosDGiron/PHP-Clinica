<?php
class SupplierController extends Controllers {
    public function __construct() {
        parent::__construct();
    }
    
    public function index() {
        $this->view("SupplierView");
    }

    public function show() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (!$this->authMiddleware->validateToken()) return;
            $suppliers = $this->model->getSuppliers();
        
            if ($suppliers) {
                foreach ($suppliers as $supplier){
                    $response[] = [
                        'id' => $supplier->id,
                        'name' =>$supplier->name,
                        'description' =>$supplier->description,
                        'email' =>$supplier->email,
                        'phone' =>$supplier->phone,
                        'address' =>$supplier->address,
                        'active' =>$supplier->active
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
                "description" => isset($decodedData['description']) ? htmlspecialchars($decodedData['description'], ENT_QUOTES, 'UTF-8') : null,
                "email" => isset($decodedData['email']) ? filter_var($decodedData['email'], FILTER_SANITIZE_EMAIL) : null,
                "phone" => isset($decodedData['phone']) ? htmlspecialchars($decodedData['phone'], ENT_QUOTES, 'UTF-8') : null,
                "address" => isset($decodedData['address']) ? htmlspecialchars($decodedData['address'], ENT_QUOTES, 'UTF-8') : null,
                "active" => isset($decodedData['active']) ? filter_var($decodedData['active'], FILTER_SANITIZE_NUMBER_INT) : null,
                "created_by" => isset($decodedData['created_by']) ? filter_var($decodedData['created_by'], FILTER_SANITIZE_NUMBER_INT) : null,
                "updated_by" => isset($decodedData['updated_by']) ? filter_var($decodedData['updated_by'], FILTER_SANITIZE_NUMBER_INT) : null
            ];
    
            if ($this->model->insertSupplier($data)) {
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
                "description" => isset($decodedData['description']) ? htmlspecialchars($decodedData['description'], ENT_QUOTES, 'UTF-8') : null,
                "email" => isset($decodedData['email']) ? filter_var($decodedData['email'], FILTER_SANITIZE_EMAIL) : null,
                "phone" => isset($decodedData['phone']) ? htmlspecialchars($decodedData['phone'], ENT_QUOTES, 'UTF-8') : null,
                "address" => isset($decodedData['address']) ? htmlspecialchars($decodedData['address'], ENT_QUOTES, 'UTF-8') : null,
                "active" => isset($decodedData['active']) ? filter_var($decodedData['active'], FILTER_SANITIZE_NUMBER_INT) : null,
                "updated_by" => isset($decodedData['updated_by']) ? filter_var($decodedData['updated_by'], FILTER_SANITIZE_NUMBER_INT) : null
            ];
            
            if ($this->model->updateSupplier($data)) {
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
    
    
            if ($this->model->deleteSupplier($data)) {
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
            $supplier = $this->model->filterSupplier($id);
    
            if ($supplier) {
                $response = [
                    'id' => $supplier->id,
                    'name' =>$supplier->name,
                    'description' =>$supplier->description,
                    'email' =>$supplier->email,
                    'phone' =>$supplier->phone,
                    'address' =>$supplier->address,
                    'active' =>$supplier->active
                ];

                $this->jsonResponse($response);
            }
        }
    }
}
?>