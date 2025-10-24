<?php
class SaleDetailController extends Controllers {
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
                "sale_id" => isset($decodedData['sale_id']) ? filter_var($decodedData['sale_id'], FILTER_SANITIZE_NUMBER_INT) : null,
                "medicine_id" => isset($decodedData['medicine_id']) ? filter_var($decodedData['medicine_id'], FILTER_SANITIZE_NUMBER_INT) : null,
                "selling_price" => isset($decodedData['selling_price']) ? filter_var($decodedData['selling_price'], FILTER_VALIDATE_FLOAT) : null,
                "quantity" => isset($decodedData['quantity']) ? filter_var($decodedData['quantity'], FILTER_SANITIZE_NUMBER_INT) : null,
                "created_by" => isset($decodedData['created_by']) ? filter_var($decodedData['created_by'], FILTER_SANITIZE_NUMBER_INT) : null,
                "updated_by" => isset($decodedData['updated_by']) ? filter_var($decodedData['updated_by'], FILTER_SANITIZE_NUMBER_INT) : null
            ];

            if ($this->model->insertSaleDetail($data)) {
                $this->jsonResponse(["success" => true]);
            } else {
                $this->jsonResponse(["success" => false]);
            }
        }
    }
}
?>