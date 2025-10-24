<?php
class UserController extends Controllers {
    public function __construct() {
        parent::__construct();
    }
    
    public function index() {
        $this->view("UserView");
    }

    public function show() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (!$this->authMiddleware->validateToken()) return;
            $users = $this->model->getUsers();

            if ($users) {
                foreach ($users as $user) {
                    $response[] = [
                        'id' => $user->id,
                        'user_name' => $user->user_name,
                        'username' => $user->username,
                        'employee_id' => $user->employee_id,
                        'employee_name' => $user->employee_name,
                        'active' => $user->active,
                        'role_id' => $user->role_id,
                        'role_name' => $user->role_name
                    ];
                }

                $this->jsonResponse($response);
            }
        }
    }

    public function filter() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->authMiddleware->validateToken()) return;
            $input = json_decode(file_get_contents("php://input"), true);
            $id = filter_var($input['id'], FILTER_SANITIZE_NUMBER_INT) ?? null;
            $user = $this->model->filterUser(['id' => $id]);
            if ($user) {
                $response = [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'username' => $user->username,
                    'employee_id' => $user->employee_id,
                    'employee_name' => $user->employee_name,
                    'active' => $user->active,
                    'role_id' => $user->role_id,
                    'role_name' => $user->role_name
                ];
            
                $this->jsonResponse($response);
            }
        }
    }

    public function search() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->authMiddleware->validateToken()) return;
            $input = json_decode(file_get_contents("php://input"), true);
            $username = htmlspecialchars($input['username'], ENT_QUOTES, 'UTF-8') ?? null;
            $user = $this->model->searchUsername(['username' => $username]);
            if ($user) {
                $response = [
                    'username' => $user->username
                ];
            
                $this->jsonResponse($response);
            }
        }
    }

    public function insert() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->authMiddleware->validateToken()) return;
            $input = json_decode(file_get_contents("php://input"), true);

            $data = [
                "role_id" => isset($input['role_id']) ? filter_var($input['role_id'], FILTER_SANITIZE_NUMBER_INT) : null,
                "first_name" => isset($input['first_name']) ? htmlspecialchars($input['first_name'], ENT_QUOTES, 'UTF-8') : null,
                "last_name" => isset($input['last_name']) ? htmlspecialchars($input['last_name'], ENT_QUOTES, 'UTF-8') : null,
                "username" => isset($input['username']) ? htmlspecialchars($input['username'], ENT_QUOTES, 'UTF-8') : null,
                "employee_id" => isset($input['employee_id']) ? filter_var($input['employee_id'], FILTER_SANITIZE_NUMBER_INT) : null,
                "password" => isset($input['password']) ? htmlspecialchars($input['password'], ENT_QUOTES, 'UTF-8') : null,
                "active" => isset($input['active']) ? filter_var($input['active'], FILTER_SANITIZE_NUMBER_INT) : null,
                "user_id" => isset($input['user_id']) ? filter_var($input['user_id'], FILTER_SANITIZE_NUMBER_INT) : null
            ];
            
            if ($this->model->insertUser($data)) {
                $this->jsonResponse(["success" => true]);
            } else {
                $this->jsonResponse(["success" => false]);
            }
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->authMiddleware->validateToken()) return;
            $input = json_decode(file_get_contents("php://input"), true);

            $data = [
                "role_id" => isset($input['role_id']) ? filter_var($input['role_id'], FILTER_SANITIZE_NUMBER_INT) : null,
                "first_name" => isset($input['first_name']) ? htmlspecialchars($input['first_name'], ENT_QUOTES, 'UTF-8') : null,
                "last_name" => isset($input['last_name']) ? htmlspecialchars($input['last_name'], ENT_QUOTES, 'UTF-8') : null,
                "employee_id" => isset($input['employee_id']) ? filter_var($input['employee_id'], FILTER_SANITIZE_NUMBER_INT) : null,
                "password" => isset($input['password']) ? htmlspecialchars($input['password'], ENT_QUOTES, 'UTF-8') : null,
                "active" => isset($input['active']) ? filter_var($input['active'], FILTER_SANITIZE_NUMBER_INT) : null,
                "user_id" => isset($input['user_id']) ? filter_var($input['user_id'], FILTER_SANITIZE_NUMBER_INT) : null,
                "id" => isset($input['id']) ? filter_var($input['id'], FILTER_SANITIZE_NUMBER_INT) : null
            ];

            if ($this->model->updateUser($data)) {
                $this->jsonResponse(["success" => true]);
            } else {
                $this->jsonResponse(["success" => false]);
            }
        }
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->authMiddleware->validateToken()) return;
            $input = json_decode(file_get_contents("php://input"), true);
            
            $data = [
                "user_id" => isset($input['user_id']) ? filter_var($input['user_id'], FILTER_SANITIZE_NUMBER_INT) : null,
                "id" => isset($input['id']) ? filter_var($input['id'], FILTER_SANITIZE_NUMBER_INT) : null
            ];

            if ($this->model->deleteUser($data)) {
                $this->jsonResponse(["success" => true]);
            } else {
                $this->jsonResponse(["success" => false]);
            }
        }
    }
}
?>