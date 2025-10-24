<?php
class BatchController extends Controllers {
    public function __construct() {
        parent::__construct();
    }
    
    public function index() {
        $this->view("BatchView");
    }

    public function show() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (!$this->authMiddleware->validateToken()) return;
            $batchs = $this->model->getBatchs();
        
            if ($batchs) {
                foreach ($batchs as $batch){
                    $response[] = [
                        'id' => $batch->id,
                        'medicine_id' =>$batch->medicine_id,
                        'medicine_name' =>$batch->medicine_name,
                        'supplier_id' =>$batch->supplier_id,
                        'supplier_name' =>$batch->supplier_name,
                        'branch_id' =>$batch->branch_id,
                        'branch_name' =>$batch->branch_name,
                        'purchase_price' =>$batch->purchase_price,
                        'quantity' =>$batch->quantity,
                        'created_at' => $batch->created_at,
                        'expiration_date' =>$batch->expiration_date
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
                "medicine_id" => isset($decodedData['medicine_id']) ? filter_var($decodedData['medicine_id'], FILTER_SANITIZE_NUMBER_INT) : null,
                "supplier_id" => isset($decodedData['supplier_id']) ? filter_var($decodedData['supplier_id'], FILTER_SANITIZE_NUMBER_INT) : null,
                "branch_id" => isset($decodedData['branch_id']) ? filter_var($decodedData['branch_id'], FILTER_SANITIZE_NUMBER_INT) : null,
                "purchase_price" => isset($decodedData['purchase_price']) ? filter_var($decodedData['purchase_price'], FILTER_VALIDATE_FLOAT) : null,
                "quantity" => isset($decodedData['quantity']) ? filter_var($decodedData['quantity'], FILTER_SANITIZE_NUMBER_INT) : null,
                "expiration_date" => isset($decodedData['expiration_date']) ? htmlspecialchars($decodedData['expiration_date'], ENT_QUOTES, 'UTF-8') : null,
                "created_by" => isset($decodedData['created_by']) ? filter_var($decodedData['created_by'], FILTER_SANITIZE_NUMBER_INT) : null,
                "updated_by" => isset($decodedData['updated_by']) ? filter_var($decodedData['updated_by'], FILTER_SANITIZE_NUMBER_INT) : null
            ];

            $batchId = $this->model->insertBatch($data);
    
            if ($batchId) {
                $this->jsonResponse(["success" => true, "batch_id" => $batchId]);
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
                "medicine_id" => isset($decodedData['medicine_id']) ? filter_var($decodedData['medicine_id'], FILTER_SANITIZE_NUMBER_INT) : null,
                "supplier_id" => isset($decodedData['supplier_id']) ? filter_var($decodedData['supplier_id'], FILTER_SANITIZE_NUMBER_INT) : null,
                "branch_id" => isset($decodedData['branch_id']) ? filter_var($decodedData['branch_id'], FILTER_SANITIZE_NUMBER_INT) : null,
                "purchase_price" => isset($decodedData['purchase_price']) ? filter_var($decodedData['purchase_price'], FILTER_VALIDATE_FLOAT) : null,
                "expiration_date" => isset($decodedData['expiration_date']) ? htmlspecialchars($decodedData['expiration_date'], ENT_QUOTES, 'UTF-8') : null,
                "updated_by" => isset($decodedData['updated_by']) ? filter_var($decodedData['updated_by'], FILTER_SANITIZE_NUMBER_INT) : null
            ];
            
            if ($this->model->updateBatch($data)) {
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
    
    
            if ($this->model->deleteBatch($data)) {
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
            $batch = $this->model->filterBatch($id);
    
            if ($batch) {
                $response = [
                    'id' => $batch->id,
                    'medicine_id' =>$batch->medicine_id,
                    'medicine_name' =>$batch->medicine_name,
                    'supplier_id' =>$batch->supplier_id,
                    'supplier_name' =>$batch->supplier_name,
                    'branch_id' =>$batch->branch_id,
                    'branch_name' =>$batch->branch_name,
                    'purchase_price' =>$batch->purchase_price,
                    'quantity' =>$batch->quantity,
                    'created_at' =>$batch->created_at,
                    'expiration_date' =>$batch->expiration_date
                ];

                $this->jsonResponse($response);
            }
        }
    }
}
?>