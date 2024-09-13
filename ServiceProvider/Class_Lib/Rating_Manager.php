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
        $query='SELECT COUNT(*) AS "count" FROM event_rating WHERE ticket_id=:ticket_id LIMIT 1';
        $statment=$this->conn->prepare($query);
        $statment->bindValue(":ticket_id",$ticket_id);
        $statment->execute();
        $result=$statment->fetch(PDO::FETCH_DEFAULT);
        $statment->closeCursor();
        return $result["count"];
    }
    public function getHostRating($id){
        $query='SELECT host_rating FROM users WHERE user_id=:user_id LIMIT 1';
        $statment=$this->conn->prepare($query);
        $statment->bindValue(":user_id",$id);
        $statment->execute();
        $result=$statment->fetch(PDO::FETCH_DEFAULT);
        $statment->closeCursor();
        return $result;
    }
    public function rateEvent(){
        //does the user rating this event own this the ticket?
        $query="SELECT ticket_id FROM tickets WHERE user_id=:user_id AND ticket_id=:ticket_id LIMIT 1";
        $statment=$this->conn->prepare($query); 
        $statment->bindValue(":ticket_id",$_POST["ticket_id"]);
        $statment->bindValue(":user_id",$_SESSION["user_id"]);
        $statment->execute();
        $result=$statment->fetch(PDO::FETCH_ASSOC);
        if(!empty($result)){
            if(!$this->isEventRated($_POST["ticket_id"])){
                $query="INSERT INTO event_rating(event_id,ticket_id,rating) VALUES(:event_id,:ticket_id,:rating)";
                $statment=$this->conn->prepare($query);
                $statment->bindValue(":event_id",$_POST["event_id"]);
                $statment->bindValue(":ticket_id",$_POST["ticket_id"]);
                $statment->bindValue(":rating",$_POST["rating"]);
                $statment->execute();
                return true;
            }
            else{
                return false;
            }
        }
        else{
            //In this case a user is attempting to rate a ticket they do not own so the session needs to be destroyed.
            session_destroy();
            return false;
        }
    }
}


?>