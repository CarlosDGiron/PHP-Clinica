<?php
class RoomAvailableModel{
    private $db;

    public function __construct(){
        $this->db = new Database;
    }
    
    public function closeConnection() {
        $this->db->closeConnection();
    }

    public function getRoomsAvailables(){
        $this->db->query(
            "SELECT r.`name` AS room_name,
                b.`name` AS branch_name,
                IFNULL(occupied_quantity, 0) AS occupied_quantity,
                r.capacity - IFNULL(occupied_quantity, 0) AS available_quantity
            FROM room AS r
            INNER JOIN branch AS b
            ON r.branch_id = b.id
            LEFT JOIN (
            	SELECT ra.room_id,
	            	ra.branch_id,
					COUNT(*) AS occupied_quantity
	            FROM room_assignment AS ra
	            WHERE ra.deleted_at IS NULL
	            AND ra.status = 1
	            GROUP BY ra.room_id,
	            	ra.branch_id
			) AS rb
			ON r.id = rb.room_id
			AND r.branch_id = rb.branch_id
            WHERE r.deleted_at IS NULL
            AND b.deleted_at IS NULL;"
        );

        $row = $this->db->records();
        return $row;
    }
}
?>