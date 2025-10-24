<?php
class AuthMiddleware {
    private $model;

    public function __construct() {
        $this->model();
    }

    public function model(){
        $routClass = "../app/Models/MiddlewareModel.php";
        
        if(file_exists($routClass)){
            require_once $routClass;
            $this->model = new MiddlewareModel;
        }
    }

    public function validateToken() {
        // Intentar extraer la cabecera Authorization desde varias fuentes (case-insensitive)
        $token = null;
        $headers = [];

        if (function_exists('getallheaders')) {
            $headers = getallheaders();
        } elseif (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
        }

        // Normalizar keys a minúsculas para búsquedas case-insensitive
        $normalized = [];
        foreach ($headers as $k => $v) {
            $normalized[strtolower($k)] = $v;
        }

        if (isset($normalized['authorization'])) {
            // Eliminar el prefijo Bearer (case-insensitive)
            $token = preg_replace('/^bearer\s+/i', '', $normalized['authorization']);
        }

        // Fallback a variables de servidor que a veces contienen la cabecera cuando PHP corre como CGI/FastCGI
        if (!$token && !empty($_SERVER['HTTP_AUTHORIZATION'])) {
            $token = preg_replace('/^bearer\s+/i', '', $_SERVER['HTTP_AUTHORIZATION']);
        }

        if (!$token && !empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $token = preg_replace('/^bearer\s+/i', '', $_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
        }

        if ($token) {
            $token = trim($token);
        }

        if ($token) {
            $session = $this->model->getSessionToken(['token' => $token]);
            $session = $this->model->getSessionToken(['token' => $token]);
            
            if ($session) {
                $tokenData = [
                    "token" => $token,
                    "expires_at" => date('Y-m-d H:i:s', strtotime('+' . TOKEN_TTL . ' seconds'))
                ];

                $this->model->updateSessionToken($tokenData);
                $user = $this->model->getUser(['user_id' => $session->user_id]);

                if (!$user) {
                    http_response_code(401);
                    return false;
                }
                
                $modules = $this->model->getModules(['role_id' => $user->role_id]);
                return [
                    "success" => true,
                    'user' => $user,
                    'modules' => $modules
                ];
            } else {
                http_response_code(401);
                return false;
            }
        } else {
            // No se encontró token en las cabeceras
            http_response_code(401);
            return false;
        }
    }
}
?>