<?php
class SaleDetailModel{
    private $db;

    public function __construct(){
        $this->db = new Database;
    }
    
    public function closeConnection() {
        $this->db->closeConnection();
    }

    public function insertSaleDetail($data){
        $this->db->query(
            "INSERT INTO sale_detail (sale_id, medicine_id, selling_price, quantity, created_by, updated_by)
            VALUES (:sale_id, :medicine_id, :selling_price, :quantity, :created_by, :updated_by);"
        );

        $this->db->bind(":sale_id", $data["sale_id"]);
        $this->db->bind(":medicine_id", $data["medicine_id"]);
        $this->db->bind(":selling_price", $data["selling_price"]);
        $this->db->bind(":quantity", $data["quantity"]);
        $this->db->bind(":created_by", $data["created_by"]);
        $this->db->bind(":updated_by", $data["updated_by"]);
        
        if($this->db->execute()){
            return true;
        } else{
            return false;
        }
    }
}
?>