
<?php
include_once("DB_Access.php");

class Ticket_Manager{

    private $db;

    private $conn;

    private $error=[];
    public function __construct() {
        $this->db=new DB_Access();
        $this->db->setConnection();
        $this->conn=$this->db->getConnection();
    }

    public function getUserTickets(){
        $query="SELECT * FROM tickets WHERE user_id=:attendant_id";
        $statment=$this->conn->prepare($query);
        $statment->bindValue(":attendant_id",$_SESSION["user_id"]);
        $statment->execute();
        $result=$statment->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

}

?>