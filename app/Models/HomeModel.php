<?php
class HomeModel{
    private $db;

    public function __construct(){
        $this->db = new Database;
    }
    
    public function getLoginUser($data){
        $this->db->query(
            "SELECT u.id AS user_id,
                CONCAT(u.first_name, ' ', u.last_name) AS user_name,
                r.`name` AS role_name
            FROM `user` AS u
            INNER JOIN `role` AS r
            ON u.role_id = r.id
            WHERE u.deleted_at IS NULL
            AND r.deleted_at IS NULL
            AND u.active = 1
            AND u.email = :email
            AND u.password = SHA2(:password , 256) LIMIT 1;"
        );

        $this->db->bind(":email", $data["login_email"]);
        $this->db->bind(":password", $data["login_password"]);
        $row = $this->db->record();
        return $row;
    }

    public function storeSessionToken($data){
        $this->db->query(
            "INSERT INTO session_tokens (user_id, token, expires_at, ip_address, user_agent, created_by, updated_by)
            VALUES (:user_id, :token, :expires_at, :ip_address, :user_agent, :user_id, :user_id);"
        );

        $this->db->bind(":user_id", $data["user_id"]);
        $this->db->bind(":token", $data["token"]);
        $this->db->bind(":expires_at", $data["expires_at"]);
        $this->db->bind(":ip_address", $data["ip_address"]);
        $this->db->bind(":user_agent", $data["user_agent"]);
        if($this->db->execute()){
            return true;
        } else{
            return false;
        }
    }

    public function deleteSessionToken($data){
        $this->db->query(
            "UPDATE session_tokens SET deleted_at = CURDATE(), deleted_by = :user_id
            WHERE user_id = :user_id AND deleted_at IS NULL;"
        );

        $this->db->bind(":user_id", $data["user_id"]);
        if($this->db->execute()){
            return true;
        } else{
            return false;
        }
    }

    public function getLoginView($data){
        $this->db->query(
            "SELECT v.`name` AS view
            FROM `user` AS u
            INNER JOIN `role` AS r
            ON u.role_id = r.id
            INNER JOIN `view` AS v
            ON r.view_id = v.id
            WHERE u.deleted_at IS NULL
            AND u.active = 1
            AND u.email = :email
            LIMIT 1;"
        );

        $this->db->bind(":email", $data["login_email"]);
        $row = $this->db->record();
        return $row;
    }

    public function getRegisterUser($data){
        $this->db->query(
            "SELECT id AS user_id,
                CONCAT(first_name, ' ', last_name) AS user_name,
                email
            FROM `user`
            WHERE deleted_at IS NULL
            AND active = 1 
            AND email = :email
            LIMIT 1;");
        $this->db->bind(":email", $data["register_email"]);
        $row = $this->db->record();
        return $row;
    }

    public function getRegisterView($data){
        $this->db->query(
            "SELECT v.`name` AS view
            FROM `user` AS u
            INNER JOIN `role` AS r
            ON u.role_id = r.id
            INNER JOIN `view` AS v
            ON r.view_id = v.id
            WHERE u.deleted_at IS NULL
            AND u.active = 1
            AND u.email = :email
            LIMIT 1;"
        );

        $this->db->bind(":email", $data["register_email"]);
        $row = $this->db->record();
        return $row;
    }

    public function getProduct($data){
        $this->db->query(
            "SELECT p.id
            FROM product AS p
            WHERE p.`active` = 1
            AND p.deleted_at IS NULL
            AND p.`name` = :product
            LIMIT 1;"
        );

        $this->db->bind(":product", $data["reservation_product"]);
        $row = $this->db->record();
        return $row;
    }

    public function getReservationHour($data){
        $this->db->query(
            "SELECT rh.id
            FROM reservation_hour AS rh
            WHERE rh.`active` = 1
            AND rh.deleted_at IS NULL
            AND rh.`name` = :hour
            LIMIT 1;"
        );

        $this->db->bind(":hour", $data["reservation_hour"]);
        $row = $this->db->record();
        return $row;
    }

    public function insertUser($data){
        $this->db->query(
            "INSERT INTO user(first_name, last_name, email, phone_number, password, address) 
            VALUES (:first_name, :last_name, :email, :phone_number, SHA2( :password, 256), :address)"
        );

        $this->db->bind(":first_name", $data["register_first_name"]);
        $this->db->bind(":last_name", $data["register_last_name"]);
        $this->db->bind(":email", $data["register_email"]);
        $this->db->bind(":phone_number", $data["register_phone"]);
        $this->db->bind(":password", $data["register_password"]);
        $this->db->bind(":address", $data["register_address"]);
        
        if($this->db->execute()){
            return true;
        } else{
            return false;
        }
    }

    public function insertReservation($data){
        $this->db->query(
            "INSERT INTO reservation (first_name, last_name, email, phone_number, product_id, product_quantity, address, reservation_date, reservation_hour_id)
            VALUES(:first_name, :last_name, :email, :phone_number, :product_id, :product_quantity, :address, :date, :hour_id);"
        );

        $this->db->bind(":first_name", $data["reservation_first_name"]);
        $this->db->bind(":last_name", $data["reservation_last_name"]);
        $this->db->bind(":email", $data["reservation_email"]);
        $this->db->bind(":phone_number", $data["reservation_phone_number"]);
        $this->db->bind(":product_id", $data["product_id"]);
        $this->db->bind(":product_quantity", $data["reservation_product_quantity"]);
        $this->db->bind(":address", $data["reservation_address"]);
        $this->db->bind(":date", $data["reservation_date"]);
        $this->db->bind(":hour_id", $data["hour_id"]);
        if($this->db->execute()){
            return true;
        } else{
            return false;
        }
    }

    public function getAvailableHour($data){
        $this->db->query(
            "SELECT rh.`name` AS reservation_hour
            FROM reservation_hour AS rh
            WHERE rh.`active` = 1
            AND rh.deleted_at IS NULL
            AND NOT EXISTS (
                SELECT 1
                FROM reservation AS r
                WHERE r.reservation_hour_id = rh.id
                AND r.deleted_at IS NULL
                AND r.reservation_date = :date
            );"
        );

        $this->db->bind(":date", $data["date"]);
        $row = $this->db->records();
        return $row;
    }
    
    public function closeConnection() {
        $this->db->closeConnection();
    }
}
?>
