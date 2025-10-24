<?php
class LoginController extends Controllers {
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->view("LoginView");
    }

    public function token() {
        $data = $this->authMiddleware->validateToken();
        $response = $this->getUserAndModules($data);
        $this->jsonResponse($response);
    }

    public function login() {
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            $input = json_decode(file_get_contents("php://input"), true);
            $username = filter_var($input['username'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
            $password = filter_var($input['password'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
            $user = $this->model->getLoginUser(['username' => $username, 'password' => $password]);

            if($user){
                $tokenData = [
                    "user_id" => $user->user_id,
                    "token" => bin2hex(random_bytes(32)),
                    "expires_at" => date('Y-m-d H:i:s', strtotime('+' . TOKEN_TTL . ' seconds')),
                    "ip_address" => $_SERVER['REMOTE_ADDR'],
                    "user_agent" => $_SERVER['HTTP_USER_AGENT']
                ];

                $this->model->deleteSessionToken(['user_id' => $user->user_id]);
                $this->model->storeSessionToken($tokenData);

                $response = [
                    "success" => true,
                    'token' => $tokenData["token"]
                ];
                $this->jsonResponse($response);
            } else{
                $this->jsonResponse(["success" => false, "message" => "Usuario o contraseña incorrectos."]);
            }
        }
    }

    public function logout()  {
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            $input = json_decode(file_get_contents("php://input"), true);
            $token = filter_var($input['token'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
            $session = $this->model->getSessionToken(['token' => $token]);
            
            if($session) {
                $this->model->deleteSessionToken(['user_id' => $session->user_id]);
                $this->jsonResponse(["success" => true, "message" => "Sesión cerrada exitosamente."]);
            } else {
                $this->jsonResponse(["success" => false, "message" => "Token inválido o ya expirado."]);
            }
        }
    }    
}
?>