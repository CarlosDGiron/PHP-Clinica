<?php
/*
La clase Core mapea la url ingresada en el navegador:
    1 - Controlador
    2 - Método
    3 - Parámetro

    Ejemplo: /articulos/actualizar/4
*/
Class Core{
    protected $controller = "LoginController";
    protected $method = "index";
    protected $parameters = [];

    public function __construct(){
        $url = $this->getUrl();
        
        //Valida si en la url se ha llamado a un método
        if(isset($url) && isset(ROUTE_MAP[strtolower($url[0])])){
            $url[0] = ROUTE_MAP[strtolower($url[0])];

            if(file_exists("../app/Controllers/".$url[0].".php")){
                $this->controller = $url[0];
                unset($url[0]);
            }
        } else{
            unset($url);
        }
        
        //Se requiere el controlador
        require_once("../app/Controllers/".$this->controller.".php");
        $this->controller = new $this->controller;
        
        //Validar si en la url se ha llamado a un método
        if(isset($url[1]) && isset(ROUTE_METHOD_MAP[strtolower($url[1])])){
            $url[1] = ROUTE_METHOD_MAP[strtolower($url[1])];
            

            if(method_exists($this->controller, $url[1])){
                $this->method = $url[1];
                unset($url[1]);
            }
        }
        
        //Obtener los parámetros de la url
        $this->parameters = isset($url) ? array_values($url) : [];

        //Se llama al controlador, metodo y parametros encontrodados en la url
        call_user_func_array([$this->controller, $this->method], $this->parameters);
    }
    
    //Función para capturar la url
    public function getUrl(){
        if(isset($_GET["url"])){
            $url = rtrim($_GET["url"], "/");
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode("/", $url);
            return $url;
        }
    }
}
?>