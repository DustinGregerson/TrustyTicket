<?php
include_once("DB_Access.php");
class Rating_Manager{

    private $db;

    private $conn;

    private $error=[];

    public function __construct() {
        $this->db=new DB_Access();
        $this->db->setConnection();
        $this->conn=$this->db->getConnection();
    }

    public function isEventRated($ticket_id){
        $query='SELECT COUNT(*) AS "count" FROM event_rating WHERE ticket_id=:ticket_id';
        $statment=$this->conn->prepare($query);
        $statment->bindValue(":ticket_id",$ticket_id);
        $statment->execute();
        $result=$statment->fetch(PDO::FETCH_DEFAULT);
        $statment->closeCursor();
        return $result["count"];
    }
    public function isHostRated($ticket_id){
        $query='SELECT COUNT(*) AS "count" FROM host_rating WHERE ticket_id=:ticket_id';
        $statment=$this->conn->prepare($query);
        $statment->bindValue(":ticket_id",$ticket_id);
        $statment->execute();
        $result=$statment->fetch(PDO::FETCH_DEFAULT);
        $statment->closeCursor();
        return $result["count"];
    }
}


?>