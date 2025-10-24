<?php
//Función para redirijir a una pagina
function redirect($page){
    header("location: ".URL_ROUTE.$page);
}

//Función que imprime arreglos
function arrayDebug($data){
    $format = print_r("<pre>");
    $format .= print_r($data);
    $format .= print_r("</pre>");
    return $format;
}
?>