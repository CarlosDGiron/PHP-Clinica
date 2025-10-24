<?php
class RoomAvailableController extends Controllers {
    public function __construct() {
        parent::__construct();
        $this->enableCors();
    }
    
    public function index() {
        $this->view("RoomAvailableView");
    }

    private function enableCors() {
        // Permitir el acceso desde cualquier origen, ideal solo en desarrollo
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: X-Api-Key, Authorization, Content-Type");

        // Si es una solicitud OPTIONS (preflight), finalizar aquí
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }
    }


    public function show() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // if (!$this->authMiddleware->validateToken()) return;
            $roomsAvailables = $this->model->getRoomsAvailables();
        
            if ($roomsAvailables) {
                foreach ($roomsAvailables as $roomAvailable){
                    $response[] = [
                        'room_name' => $roomAvailable->room_name,
                        'branch_name' =>$roomAvailable->branch_name,
                        'occupied_quantity' =>$roomAvailable->occupied_quantity,
                        'available_quantity' =>$roomAvailable->available_quantity
                    ];
                }

                $this->jsonResponse($response);
            }
        }
    }
}
?>