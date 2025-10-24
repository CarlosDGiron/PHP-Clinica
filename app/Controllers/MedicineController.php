<?php
class MedicineController extends Controllers {
    public function __construct() {
        parent::__construct();
        $this->enableCors();
    }
    
    public function index() {
        $this->view("MedicineView");
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
            $headers = getallheaders();
            $secretKey = isset($headers['X-Api-Key']) ? $headers['X-Api-Key'] : null;

            if (!$this->authMiddleware->validateToken() && $secretKey !== API_SECRET_KEY) {
                return;
            } else {
                http_response_code(200);
            }

            $medicines = $this->model->getMedicines();
        
            if ($medicines) {
                foreach ($medicines as $medicine){
                    $response[] = [
                        'id' => $medicine->id,
                        'name' =>$medicine->name,
                        'description' =>$medicine->description,
                        'selling_price' =>$medicine->selling_price,
                        'quantity' =>$medicine->quantity,
                        'brand' =>$medicine->brand,
                        'image_path' =>$medicine->image_path,
                        'active' =>$medicine->active
                    ];
                }   
                $this->jsonResponse($response);
            }
        }
    }

    public function insert() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->authMiddleware->validateToken()) return;
    
            // Procesar los datos de texto enviados
            $data = [
                "name" => isset($_POST['name']) ? htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8') : null,
                "description" => isset($_POST['description']) ? htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8') : null,
                "selling_price" => isset($_POST['selling_price']) ? filter_var($_POST['selling_price'], FILTER_VALIDATE_FLOAT) : null,
                "brand" => isset($_POST['brand']) ? htmlspecialchars($_POST['brand'], ENT_QUOTES, 'UTF-8') : null,
                "active" => isset($_POST['active']) ? filter_var($_POST['active'], FILTER_SANITIZE_NUMBER_INT) : null,
                "created_by" => isset($_POST['created_by']) ? filter_var($_POST['created_by'], FILTER_SANITIZE_NUMBER_INT) : null,
                "updated_by" => isset($_POST['updated_by']) ? filter_var($_POST['updated_by'], FILTER_SANITIZE_NUMBER_INT) : null,
            ];
    
            // Procesar el archivo de imagen si está presente
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $fileType = mime_content_type($_FILES['image']['tmp_name']);
                $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    
                if (in_array($fileType, $allowedTypes)) {
                    // Definir el directorio de destino para la imagen
                    $target_dir = "img/medicine/";
                    $fileName = uniqid() . "_" . basename($_FILES['image']['name']);
                    $target_file = $target_dir . $fileName;
    
                    // Mover el archivo cargado a su destino
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                        $data['image_path'] = $target_file; // Almacena la ruta de la imagen en los datos
                    } else {
                        $this->jsonResponse(["success" => false, "message" => "Error al subir la imagen."]);
                        return;
                    }
                } else {
                    $this->jsonResponse(["success" => false, "message" => "Formato de archivo no permitido."]);
                    return;
                }
            } else {
                $data['image_path'] = null; // En caso de que no se suba una imagen, la ruta es nula
            }
    
            // Llamar al modelo para insertar los datos
            if ($this->model->insertMedicine($data)) {
                $this->jsonResponse(["success" => true]);
            } else {
                $this->jsonResponse(["success" => false]);
            }
        }
    }
    
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->authMiddleware->validateToken()) return;
    
            // Procesar los datos de texto enviados a través de $_POST
            $data = [
                "id" => isset($_POST['id']) ? filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT) : null,
                "name" => isset($_POST['name']) ? htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8') : null,
                "description" => isset($_POST['description']) ? htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8') : null,
                "selling_price" => isset($_POST['selling_price']) ? filter_var($_POST['selling_price'], FILTER_VALIDATE_FLOAT) : null,
                "brand" => isset($_POST['brand']) ? htmlspecialchars($_POST['brand'], ENT_QUOTES, 'UTF-8') : null,
                "active" => isset($_POST['active']) ? filter_var($_POST['active'], FILTER_SANITIZE_NUMBER_INT) : null,
                "updated_by" => isset($_POST['updated_by']) ? filter_var($_POST['updated_by'], FILTER_SANITIZE_NUMBER_INT) : null,
            ];
    
            // Verificar si se ha enviado una nueva imagen
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $fileType = mime_content_type($_FILES['image']['tmp_name']);
                $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    
                if (in_array($fileType, $allowedTypes)) {
                    // Definir el directorio y el nombre del archivo
                    $target_dir = "img/medicine/";
                    $fileName = uniqid() . "_" . basename($_FILES['image']['name']);
                    $target_file = $target_dir . $fileName;
    
                    // Intentar mover la nueva imagen al directorio
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                        $data['image_path'] = $target_file; // Guardar la ruta de la nueva imagen en $data
    
                        // Obtener la ruta de la imagen actual
                        $currentImage = $this->model->filterMedicine($data['id']);
    
                        // Eliminar la imagen anterior si existe y es diferente de la nueva
                        if ($currentImage->image_path && file_exists($currentImage->image_path)) {
                            unlink($currentImage->image_path);
                        }
                    } else {
                        $this->jsonResponse(["success" => false, "message" => "Error al subir la nueva imagen."]);
                        return;
                    }
                } else {
                    $this->jsonResponse(["success" => false, "message" => "Formato de archivo no permitido."]);
                    return;
                }
            } else {
                $data['image_path'] = null;
            }
    
            // Llamar al modelo para actualizar los datos
            if ($this->model->updateMedicine($data)) {
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
    
    
            if ($this->model->deleteMedicine($data)) {
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
            $medicine = $this->model->filterMedicine($id);
    
            if ($medicine) {
                $response = [
                    'id' => $medicine->id,
                    'name' =>$medicine->name,
                    'description' =>$medicine->description,
                    'selling_price' =>$medicine->selling_price,
                    'quantity' =>$medicine->quantity,
                    'brand' =>$medicine->brand,
                    'active' =>$medicine->active
                ];
            
                $this->jsonResponse($response);
            }
        }   
    }
}
?>