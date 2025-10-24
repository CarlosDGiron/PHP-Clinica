<?php
class BatchModel{
    private $db;

    public function __construct(){
        $this->db = new Database;
    }
    
    public function closeConnection() {
        $this->db->closeConnection();
    }

    public function getBatchs(){
        $this->db->query(
            "SELECT b.id,
            	m.id AS medicine_id,
            	m.`name` AS medicine_name,
            	s.id AS supplier_id,
            	s.`name` AS supplier_name,
            	ba.id AS branch_id,
            	ba.`name` AS branch_name,
                b.purchase_price,
            	b.quantity,
            	b.created_at,
            	b.expiration_date
            FROM batch AS b
            INNER JOIN inventory AS i
            ON b.id = i.batch_id
            AND i.deleted_at IS NULL
            LEFT JOIN medicine AS m
            ON b.medicine_id = m.id
            AND m.deleted_at IS NULL
            LEFT JOIN supplier AS s
            ON b.supplier_id = s.id
            AND s.deleted_at IS NULL
            LEFT JOIN branch AS ba
            ON i.branch_id = ba.id
            AND ba.deleted_at IS NULL
            WHERE b.deleted_at IS NULL;"
        );

        $row = $this->db->records();
        return $row;
    }

    public function insertBatch($data){
        $this->db->query(
            "INSERT INTO batch
             (medicine_id,
			  supplier_id,
              purchase_price,
			  quantity,
              expiration_date,
              created_by,
              updated_by)
             VALUES
             (
              :medicine_id,
              :supplier_id,
              :purchase_price,
              :quantity,
              :expiration_date,
			  :created_by,
			  :updated_by);"
        );

        $this->db->bind(":expiration_date", $data["expiration_date"]);
        $this->db->bind(":purchase_price", $data["purchase_price"]);
        $this->db->bind(":quantity", $data["quantity"]);
        $this->db->bind(":medicine_id", $data["medicine_id"]);
        $this->db->bind(":supplier_id", $data["supplier_id"]);
        $this->db->bind(":created_by", $data["created_by"]);
        $this->db->bind(":updated_by", $data["updated_by"]);
        
        if($this->db->execute()){
            return $this->db->lastInsertId();
        } else{
            return false;
        }
    }

    public function updateBatch($data){
        $this->db->query(
            "UPDATE batch
                    SET expiration_date = :expiration_date,
                    purchase_price = :purchase_price,
                    medicine_id = :medicine_id,
                    supplier_id = :supplier_id,
                    updated_at = CURRENT_TIMESTAMP(),
                    updated_by = :updated_by
                    WHERE id = :id;"
        );

        $this->db->bind(":id", $data["id"]);
        $this->db->bind(":expiration_date", $data["expiration_date"]);
        $this->db->bind(":purchase_price", $data["purchase_price"]);
        $this->db->bind(":medicine_id", $data["medicine_id"]);
        $this->db->bind(":supplier_id", $data["supplier_id"]);
        $this->db->bind(":updated_by", $data["updated_by"]);
        if($this->db->execute()){
            return true;
        } else{
            return false;
        }
    }

    public function deleteBatch($data){
        $this->db->query(
            "UPDATE batch
            SET deleted_at = CURRENT_TIMESTAMP(),
            deleted_by = :deleted_by
            WHERE id = :id;"
        );

        $this->db->bind(":id", $data["id"]);
        $this->db->bind(":deleted_by", $data["deleted_by"]);

        if($this->db->execute()){
            return true;
        } else{
            return false;
        }
    }

    public function filterBatch($id){
        $this->db->query(
            "SELECT b.id,
            	b.medicine_id,
            	m.`name` AS medicine_name,
            	b.supplier_id,
            	s.`name` AS supplier_name,
            	i.branch_id,
            	ba.`name` AS branch_name,
                b.purchase_price,
            	b.quantity,
            	DATE(b.created_at) AS created_at,
            	b.expiration_date
            FROM batch AS b
            INNER JOIN medicine AS m
            ON b.medicine_id = m.id
            INNER JOIN supplier AS s
            ON b.supplier_id = s.id
            INNER JOIN inventory AS i
            ON b.id = i.batch_id
            INNER JOIN branch AS ba
            ON i.branch_id = ba.id
            WHERE b.id = :id
            AND b.deleted_at IS NULL;");

        $this->db->bind(':id', $id);

        $row = $this->db->record();
        return $row;
    }
}
?>