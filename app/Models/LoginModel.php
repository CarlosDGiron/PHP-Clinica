<?php
class LoginModel{
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function closeConnection() {
        $this->db->closeConnection();
    }
    
    public function getLoginUser($data){
        $this->db->query(
            "SELECT u.id AS user_id
            FROM `user` AS u
            LEFT JOIN `role` AS r
            ON u.role_id = r.id
            AND r.active = 1
            WHERE u.deleted_at IS NULL
            AND r.deleted_at IS NULL
            AND u.active = 1
            AND u.username = :username
            AND u.password = SHA2(:password, 256)
            LIMIT 1;"
        );

        $this->db->bind(":username", $data["username"]);
        $this->db->bind(":password", $data["password"]);
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

    public function getSessionToken($data) {
        $this->db->query(
                "SELECT *
            FROM session_tokens 
            WHERE token = :token
            AND deleted_at IS NULL
            AND expires_at > NOW()
            LIMIT 1;"
        );
        
        $this->db->bind(":token", $data["token"]);
        $row = $this->db->record();
        return $row;
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
}
?>
