<?php
class PermissionController extends Controllers {
    public function __construct() {
        parent::__construct();
    }
    
    public function index() {}

    public function filter() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->authMiddleware->validateToken()) return;
            $input = json_decode(file_get_contents("php://input"), true);
            $role_id = filter_var($input['role_id'], FILTER_SANITIZE_NUMBER_INT) ?? null;
            $permissions = $this->model->filterPermissions(['role_id' => $role_id]);

            if ($permissions) {
                foreach ($permissions as $permission) {
                    $response[] = [
                        'id' => $permission->id,
                        'module_id' => $permission->module_id,
                        'module' => $permission->module,
                        'show_operation' => $permission->show_operation,
                        'create_operation' => $permission->create_operation,
                        'update_operation' => $permission->update_operation,
                        'delete_operation' => $permission->delete_operation,
                        'cud_operation' => $permission->cud_operation
                    ];
                }
                $this->jsonResponse($response);
            }
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->authMiddleware->validateToken()) return;
            $input = json_decode(file_get_contents("php://input"), true);
            
            $data = [
                "id" => filter_var($input['id'], FILTER_SANITIZE_NUMBER_INT) ?? null,
                "role_id" => filter_var($input['role_id'], FILTER_SANITIZE_NUMBER_INT) ?? null,
                "module_id" => filter_var($input['module_id'], FILTER_SANITIZE_NUMBER_INT) ?? null,
                "show_operation" => filter_var($input['show_operation'], FILTER_SANITIZE_NUMBER_INT) ?? null,
                "create_operation" => filter_var($input['create_operation'], FILTER_SANITIZE_NUMBER_INT) ?? null,
                "update_operation" => filter_var($input['update_operation'], FILTER_SANITIZE_NUMBER_INT) ?? null,
                "delete_operation" => filter_var($input['delete_operation'], FILTER_SANITIZE_NUMBER_INT) ?? null,
                "user_id" => filter_var($input['user_id'], FILTER_SANITIZE_NUMBER_INT) ?? null
            ];

            if ($this->model->updatePermission($data)) {
                $this->jsonResponse(["success" => true]);
            } else {
                $this->jsonResponse(["success" => false]);
            }
        }
    }

    public function insert() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->authMiddleware->validateToken()) return;
            $input = json_decode(file_get_contents("php://input"), true);
            
            $data = [
                "module_id" => filter_var($input['module_id'], FILTER_SANITIZE_NUMBER_INT) ?? null,
                "role_id" => filter_var($input['role_id'], FILTER_SANITIZE_NUMBER_INT) ?? null,
                "show_operation" => filter_var($input['show_operation'], FILTER_SANITIZE_NUMBER_INT) ?? null,
                "create_operation" => filter_var($input['create_operation'], FILTER_SANITIZE_NUMBER_INT) ?? null,
                "update_operation" => filter_var($input['update_operation'], FILTER_SANITIZE_NUMBER_INT) ?? null,
                "delete_operation" => filter_var($input['delete_operation'], FILTER_SANITIZE_NUMBER_INT) ?? null,
                "user_id" => filter_var($input['user_id'], FILTER_SANITIZE_NUMBER_INT) ?? null
            ];

            if ($this->model->insertPermission($data)) {
                $this->jsonResponse(["success" => true]);
            } else {
                $this->jsonResponse(["success" => false]);
            }
        }
    }
}

?>