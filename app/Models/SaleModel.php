<?php
class SaleModel{
    private $db;

    public function __construct(){
        $this->db = new Database;
    }
    
    public function closeConnection() {
        $this->db->closeConnection();
    }

    public function insertSale($data){
        $this->db->query(
            "INSERT INTO sale (branch_id, customer, sale_date, created_by, updated_by)
            VALUES (:branch_id, :customer, :sale_date, :created_by, :updated_by);"
        );

        $this->db->bind(":branch_id", $data["branch_id"]);
        $this->db->bind(":customer", $data["customer"]);
        $this->db->bind(":sale_date", $data["sale_date"]);
        $this->db->bind(":created_by", $data["created_by"]);
        $this->db->bind(":updated_by", $data["updated_by"]);
        
        if($this->db->execute()){
            return $this->db->lastInsertId();
        } else{
            return false;
        }
    }

    public function filterInventory($data){
        $this->db->query(
            "SELECT m.id AS medicine_id,
                m.name AS medicine_name,
                mq.branch_id,
                m.selling_price,
				IFNULL(mq.quantity, 0) AS quantity
            FROM medicine AS m
            LEFT JOIN (
            	SELECT b.medicine_id,
                    i.branch_id,
	            	SUM(i.quantity) AS quantity
	            FROM inventory AS i
	            INNER JOIN batch AS b
	            ON i.batch_id = b.id
	    		WHERE i.quantity > 0
	    		AND i.deleted_at IS NULL
	    		AND b.deleted_at IS NULL
                AND i.branch_id = :branch_id
	            GROUP BY b.medicine_id,
                    i.branch_id
			) AS mq
			ON m.id = mq.medicine_id
            WHERE m.id = :medicine_id
            AND m.deleted_at IS NULL;"
        );

        $this->db->bind(':medicine_id', $data["medicine_id"]);
        $this->db->bind(':branch_id', $data["branch_id"]);
        $row = $this->db->record();
        return $row;
    }

    public function getSaleDetail($data){
        $this->db->query(
            "SELECT sd.medicine_id,
                s.branch_id,
                SUM(sd.quantity) AS quantity
            FROM sale AS s
            INNER JOIN sale_detail AS sd
            ON s.id = sd.sale_id
            WHERE s.deleted_at IS NULL
            AND sd.deleted_at IS NULL
            AND s.id = :sale_id
            GROUP BY sd.medicine_id,
                s.branch_id;"
        );

        $this->db->bind(':sale_id', $data["sale_id"]);
        $row = $this->db->records();
        return $row;
    }

    public function getInventory($data){
        $this->db->query(
            "SELECT b.id AS batch_id,
				i.branch_id,
				b.medicine_id,
				i.quantity
            FROM batch AS b
            INNER JOIN inventory AS i
            ON b.id = i.batch_id
            WHERE b.deleted_at IS NULL
            AND i.deleted_at IS NULL
            AND i.quantity > 0
            AND b.medicine_id = :medicine_id
            AND i.branch_id = :branch_id
            ORDER BY i.created_at ASC;"
        );

        $this->db->bind(':medicine_id', $data["medicine_id"]);
        $this->db->bind(':branch_id', $data["branch_id"]);
        $row = $this->db->records();
        return $row;
    }

    public function updateInventory($data){
        $this->db->query(
            "UPDATE inventory
                SET quantity = :quantity,
                updated_at = CURRENT_TIMESTAMP(),
                updated_by = :updated_by
                WHERE batch_id = :batch_id;"
        );

        $this->db->bind(":batch_id", $data["batch_id"]);
        $this->db->bind(":quantity", $data["quantity"]);
        $this->db->bind(":updated_by", $data["updated_by"]);
        if($this->db->execute()){
            return true;
        } else{
            return false;
        }
    }
}
?>
