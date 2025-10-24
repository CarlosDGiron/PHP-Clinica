<?php
class MedicineModel{
    private $db;

    public function __construct(){
        $this->db = new Database;
    }
    
    public function closeConnection() {
        $this->db->closeConnection();
    }

    public function getMedicines(){
        $this->db->query(
            "SELECT m.id,
                m.name,
                m.description,
                m.selling_price,
                IFNULL(mq.quantity, 0) AS quantity,
                m.brand,
                m.image_path,
                m.active
            FROM medicine AS m
            LEFT JOIN (
            	SELECT b.medicine_id,
	            	SUM(i.quantity) AS quantity
	            FROM inventory AS i
	            INNER JOIN batch AS b
	            ON i.batch_id = b.id
	    		WHERE i.quantity != 0
	    		AND i.deleted_at IS NULL
	    		AND b.deleted_at IS NULL
	            GROUP BY b.medicine_id
			) AS mq
			ON m.id = mq.medicine_id
            WHERE m.deleted_at IS NULL;"
        );

        $row = $this->db->records();
        return $row;
    }

    public function insertMedicine($data){
        $this->db->query(
            "INSERT INTO medicine (`name`, description, selling_price,brand, image_path, active, created_by, updated_by)
            VALUES(:name, :description, :selling_price, :brand, :image_path, :active, :created_by, :updated_by);"
        );

        $this->db->bind(":name", $data["name"]);
        $this->db->bind(":description", $data["description"]);
        $this->db->bind(":selling_price", $data["selling_price"]);
        $this->db->bind(":brand", $data["brand"]);
        $this->db->bind(":image_path", $data["image_path"]);
        $this->db->bind(":active", $data["active"]);
        $this->db->bind(":created_by", $data["created_by"]);
        $this->db->bind(":updated_by", $data["updated_by"]);
        
        if($this->db->execute()){
            return true;
        } else{
            return false;
        }
    }

    public function updateMedicine($data){
        $this->db->query(
            "UPDATE medicine
            SET `name` = :name,
            `description` = :description,
            selling_price = :selling_price,
            brand = :brand,
            image_path = IF(:image_path IS NULL, image_path, :image_path),
            active = :active,
            updated_at = CURRENT_TIMESTAMP(),
            updated_by = :updated_by
            WHERE id = :id;"
        );

        $this->db->bind(":id", $data["id"]);
        $this->db->bind(":name", $data["name"]);
        $this->db->bind(":description", $data["description"]);
        $this->db->bind(":selling_price", $data["selling_price"]);
        $this->db->bind(":brand", $data["brand"]);
        $this->db->bind(":image_path", $data["image_path"]);
        $this->db->bind(":active", $data["active"]);
        $this->db->bind(":updated_by", $data["updated_by"]);
        if($this->db->execute()){
            return true;
        } else{
            return false;
        }
    }

    public function deleteMedicine($data){
        $this->db->query(
            "UPDATE medicine
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

    public function filterMedicine($id){
        $this->db->query(
            "SELECT m.id,
                m.`name`,
                m.`description`,
                m.selling_price,
                IFNULL(mq.quantity, 0) AS quantity,
                m.brand,
                m.image_path,
                m.active
            FROM medicine AS m
            LEFT JOIN (
            	SELECT b.medicine_id,
	            	SUM(i.quantity) AS quantity
	            FROM inventory AS i
	            INNER JOIN batch AS b
	            ON i.batch_id = b.id
	    		WHERE i.quantity != 0
	    		AND i.deleted_at IS NULL
	    		AND b.deleted_at IS NULL
	            GROUP BY b.medicine_id
			) AS mq
			ON m.id = mq.medicine_id
            WHERE m.id = :id
            AND m.deleted_at IS NULL;"
        );

        $this->db->bind(':id', $id);

        $row = $this->db->record();
        return $row;
    }
}
?>