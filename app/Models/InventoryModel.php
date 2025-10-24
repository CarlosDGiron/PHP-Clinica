<?php
class InventoryModel{
    private $db;

    public function __construct(){
        $this->db = new Database;
    }
    
    public function closeConnection() {
        $this->db->closeConnection();
    }

    public function insertInventory($data){
        $this->db->query(
            "INSERT INTO inventory (batch_id, branch_id, quantity, created_by, updated_by)
            VALUES (:batch_id, :branch_id, :quantity, :created_by, :updated_by);"
        );

        $this->db->bind(":batch_id", $data["batch_id"]);
        $this->db->bind(":branch_id", $data["branch_id"]);
        $this->db->bind(":quantity", $data["quantity"]);
        $this->db->bind(":created_by", $data["created_by"]);
        $this->db->bind(":updated_by", $data["updated_by"]);
        
        if($this->db->execute()){
            return true;
        } else{
            return false;
        }
    }

    public function updateInventory($data){
        $this->db->query(
            "UPDATE inventory
                SET branch_id = :branch_id,
                updated_at = CURRENT_TIMESTAMP(),
                updated_by = :updated_by
                WHERE batch_id = :batch_id;"
        );

        $this->db->bind(":batch_id", $data["batch_id"]);
        $this->db->bind(":branch_id", $data["branch_id"]);
        $this->db->bind(":updated_by", $data["updated_by"]);
        if($this->db->execute()){
            return true;
        } else{
            return false;
        }
    }

    public function deleteInventory($data){
        $this->db->query(
            "UPDATE inventory
            SET deleted_at = CURRENT_TIMESTAMP(),
            deleted_by = :deleted_by
            WHERE batch_id = :batch_id;"
        );

        $this->db->bind(":batch_id", $data["batch_id"]);
        $this->db->bind(":deleted_by", $data["deleted_by"]);

        if($this->db->execute()){
            return true;
        } else{
            return false;
        }
    }

    public function filterInventory($id){
        $this->db->query(
            "SELECT b.id AS batch_id,
                ba.id AS branch_id,
                ba.name AS branch_name,
				b.purchase_price,
				b.quantity AS original_quantity,
				IFNULL(i.quantity, 0) AS current_quantity,
				b.expiration_date,
				i.created_at,
				i.updated_at
            FROM medicine AS m
            INNER JOIN batch AS b
            ON m.id = b.medicine_id
            INNER JOIN inventory AS i
            ON b.id = i.batch_id
            INNER JOIN branch AS ba
            ON ba.id = i.branch_id
            WHERE m.deleted_at IS NULL
            AND b.deleted_at IS NULL
            AND i.deleted_at IS NULL
            AND ba.deleted_at IS NULL
            AND i.quantity > 0
            AND m.id = :id
            ORDER BY i.created_at ASC;"
        );

        $this->db->bind(':id', $id);
        $row = $this->db->records();
        return $row;
    }
}
?>