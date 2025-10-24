<?php
class Database {
    private $dbh;
    private $stmt;

    //Se creea la instancia a PDO
    public function __construct(){
        $dbh = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";".DB_CHARSET;
        $options = array(
            PDO::ATTR_PERSISTENT =>true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );

        try {
            $this->dbh = new PDO($dbh, DB_USER, DB_PASSWORD, $options);
        } catch (PDOException $e){
            echo "Error: ".$e->getMessage();
        }
    }

    //Se prepara la consulta
    public function query($sql){
        $this->stmt = $this->dbh->prepare($sql);
    }

    //Se le asigna el tipo de dato al valor ingresado
    public function bind($parameter, $value, $type = null){
        if(is_null($type)){
            switch(true){
                case is_int($value);
                    $type = PDO::PARAM_INT;
                break;

                case is_bool($value);
                    $type = PDO::PARAM_BOOL;
                break;

                case is_null($value);
                    $type = PDO::PARAM_NULL;
                break;

                default;
                    $type = PDO::PARAM_STR;
                break;
            }
        }

        $this->stmt->bindValue($parameter, $value, $type);
    }

    //Se ejecuta la consulta
    public function execute(){
        return $this->stmt->execute();
    }

    //Se obtiene un solo registro
    public function record(){
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_OBJ);
    }

    //Se obtienen varios registros
    public function records(){
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    //Se obtiene la cantidad de filas
    public function rowCount(){
        return $this->stmt->rowCount();
    }

    //Se obtiene el ultimo id insertado
    public function lastInsertId() {
        return $this->dbh->lastInsertId();
    }
    
    // Cerrar la conexión
    public function closeConnection() {
        $this->dbh = null;
    }
}
?>