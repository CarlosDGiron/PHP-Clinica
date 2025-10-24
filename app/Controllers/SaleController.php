<?php
class SaleController extends Controllers {
    public function __construct() {
        parent::__construct();
    }
    
    public function index() {
        $this->view("SaleView");
    }

    public function filter() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->authMiddleware->validateToken()) return;
            $json = file_get_contents('php://input');
            $decodedData = json_decode($json, true); 
    
            $medicine_id = isset($decodedData['medicine_id']) ? filter_var($decodedData['medicine_id'], FILTER_SANITIZE_NUMBER_INT) : null;
            $branch_id = isset($decodedData['branch_id']) ? filter_var($decodedData['branch_id'], FILTER_SANITIZE_NUMBER_INT) : null;
            $invetory = $this->model->filterInventory(['medicine_id' => $medicine_id, 'branch_id' => $branch_id]);
    
            if ($invetory) {
                $response = [
                    'medicine_id' => $invetory->medicine_id,
                    'medicine_name' => $invetory->medicine_name,
                    'branch_id' => $invetory->branch_id,
                    'selling_price' =>$invetory->selling_price,
                    'quantity' =>$invetory->quantity
                ];
            
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
                "branch_id" => isset($decodedData['branch_id']) ? filter_var($decodedData['branch_id'], FILTER_SANITIZE_NUMBER_INT) : null,
                "customer" => isset($decodedData['customer']) ? htmlspecialchars($decodedData['customer'], ENT_QUOTES, 'UTF-8') : null,
                "sale_date" => isset($decodedData['sale_date']) ? htmlspecialchars($decodedData['sale_date'], ENT_QUOTES, 'UTF-8') : null,
                "created_by" => isset($decodedData['created_by']) ? filter_var($decodedData['created_by'], FILTER_SANITIZE_NUMBER_INT) : null,
                "updated_by" => isset($decodedData['updated_by']) ? filter_var($decodedData['updated_by'], FILTER_SANITIZE_NUMBER_INT) : null
            ];

            $saleId = $this->model->insertSale($data);
    
            if ($saleId) {
                $this->jsonResponse(["success" => true, "sale_id" => $saleId]);
            } else {
                $this->jsonResponse(["success" => false]);
            }
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->authMiddleware->validateToken()) return;
            $json = file_get_contents('php://input');
            $decodedData = json_decode($json, true); 

            $sale_id = isset($decodedData['sale_id']) ? filter_var($decodedData['sale_id'], FILTER_SANITIZE_NUMBER_INT) : null;
            $updated_by = isset($decodedData['updated_by']) ? filter_var($decodedData['updated_by'], FILTER_SANITIZE_NUMBER_INT) : null;

            $sales = $this->model->getSaleDetail(['sale_id' => $sale_id]);

            foreach ($sales as $sale) {
                $data = [
                    "medicine_id" => $sale->medicine_id,
                    "branch_id" => $sale->branch_id
                ];
                
                $inventories = $this->model->getInventory($data);
                $totalQuantity = 0;

                foreach ($inventories as $inventory) {
                    if ($totalQuantity >= $sale->quantity) {
                        break;
                    }
                    
                    $requiredFromBatch = min($inventory->quantity, $sale->quantity - $totalQuantity);
                    $remainingQuantity = $inventory->quantity - $requiredFromBatch;

                    $selectedBatches = [
                        'batch_id' => $inventory->batch_id,
                        'quantity' => $remainingQuantity,
                        'updated_by' => $updated_by
                    ];
        
                    $totalQuantity += $inventory->quantity;

                    $allUpdates[] = [
                        "success" => $this->model->updateInventory($selectedBatches)
                    ];
                }
            }

            $this->jsonResponse(["updates" => $allUpdates]);
        }
    }
}
?>