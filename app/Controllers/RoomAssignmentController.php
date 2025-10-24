<?php
class RoomAssignmentController extends Controllers {
    public function __construct() {
        parent::__construct();
    }
    
    public function index() {}

    public function show() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->authMiddleware->validateToken()) return;
            $json = file_get_contents('php://input');
            $decodedData = json_decode($json, true); 
    
            $branch_id = isset($decodedData['branch_id']) ? filter_var($decodedData['branch_id'], FILTER_SANITIZE_NUMBER_INT) : null;
            $patient_id = isset($decodedData['patient_id']) ? filter_var($decodedData['patient_id'], FILTER_SANITIZE_NUMBER_INT) : null;
            $rooms = $this->model->getRooms(["branch_id" => $branch_id, "patient_id" => $patient_id]);
        
            if ($rooms) {
                foreach ($rooms as $room){
                    $response[] = [
                        'id' => $room->id,
                        'name' => $room->name
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
                "room_id" => isset($decodedData['room_id']) ? filter_var($decodedData['room_id'], FILTER_SANITIZE_NUMBER_INT) : null,
                "branch_id" => isset($decodedData['branch_id']) ? filter_var($decodedData['branch_id'], FILTER_SANITIZE_NUMBER_INT) : null,
                "status" => isset($decodedData['status']) ? filter_var($decodedData['status'], FILTER_SANITIZE_NUMBER_INT) : null,
                "created_by" => isset($decodedData['created_by']) ? filter_var($decodedData['created_by'], FILTER_SANITIZE_NUMBER_INT) : null,
                "updated_by" => isset($decodedData['updated_by']) ? filter_var($decodedData['updated_by'], FILTER_SANITIZE_NUMBER_INT) : null
            ];
    
            if ($this->model->insertRoomAssignment($data)) {
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
                "patient_id" => isset($decodedData['patient_id']) ? filter_var($decodedData['patient_id'], FILTER_SANITIZE_NUMBER_INT) : null,
                "room_id" => isset($decodedData['room_id']) ? filter_var($decodedData['room_id'], FILTER_SANITIZE_NUMBER_INT) : null,
                "branch_id" => isset($decodedData['branch_id']) ? filter_var($decodedData['branch_id'], FILTER_SANITIZE_NUMBER_INT) : null,
                "status" => isset($decodedData['status']) ? filter_var($decodedData['status'], FILTER_SANITIZE_NUMBER_INT) : null,
                "updated_by" => isset($decodedData['updated_by']) ? filter_var($decodedData['updated_by'], FILTER_SANITIZE_NUMBER_INT) : null
            ];
            
            if ($this->model->updateRoomAssignment($data)) {
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
    
            $id = isset($decodedData['patient_id']) ? filter_var($decodedData['patient_id'], FILTER_SANITIZE_NUMBER_INT) : null;
            $rooms = $this->model->filterRoomAssignment($id);
    
            if ($rooms) {
                $response = [
                    'patient_id' => $rooms->patient_id,
                    'room_id' =>$rooms->room_id,
                    'room_name' =>$rooms->room_name,
                    'branch_id' =>$rooms->branch_id,
                    'branch_name' =>$rooms->branch_name
                ];

                $this->jsonResponse($response);
            }
        }
    }

    
    public function search() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->authMiddleware->validateToken()) return;
            $json = file_get_contents('php://input');
            $decodedData = json_decode($json, true); 
    
            $id = isset($decodedData['patient_id']) ? filter_var($decodedData['patient_id'], FILTER_SANITIZE_NUMBER_INT) : null;
            $rooms = $this->model->searchRoomAssignment($id);
    
            if ($rooms) {
                $response = [
                    'patient_id' => $rooms->patient_id,
                    'room_id' =>$rooms->room_id,
                    'branch_id' =>$rooms->branch_id,
                    'status' =>$rooms->status,
                ];

                $this->jsonResponse($response);
            }
        }
    }
}
?>