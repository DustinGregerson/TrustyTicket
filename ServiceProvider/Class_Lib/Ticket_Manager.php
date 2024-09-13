
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
    public function getUserTicketsSorted(){
        if($_GET["sort"]=="ticket_check_in"){
            $query="SELECT * 
            FROM tickets
            JOIN event_dates ON event_dates.event_id=tickets.event_id
            WHERE user_id=:attendant_id AND event_dates.start_relative_to_central_time < NOW() AND 
            event_dates.end_relative_to_central_time > NOW()";
        }
        else if($_GET["sort"]=="past"){
            $query="SELECT * 
            FROM tickets
            JOIN event_dates ON event_dates.event_id=tickets.event_id
            WHERE user_id=:attendant_id AND event_dates.end_relative_to_central_time < NOW()";
        }
        else if($_GET["sort"]=="future"){
            $query="SELECT * 
            FROM tickets
            JOIN event_dates ON event_dates.event_id=tickets.event_id
            WHERE user_id=:attendant_id AND event_dates.start_relative_to_central_time > NOW()";
        }

        $statment=$this->conn->prepare($query);
        $statment->bindValue(":attendant_id",$_SESSION["user_id"]);
        $statment->execute();
        $result=$statment->fetchAll(PDO::FETCH_ASSOC);
        return $result;


    }
    public function getTicketsForEvent($event_id){
        $query='SELECT COUNT(*) AS "ticket_sold", IFNULL(SUM(charge),0) AS "total_payout"
                FROM tickets
                JOIN events ON tickets.event_id=events.event_id
                WHERE events.event_id=:event_id AND events.user_id='."'".$_SESSION["user_id"]."'";
        $statment=$this->conn->prepare($query);
        $statment->bindValue(":event_id",$event_id);
        $statment->execute();
        $result=$statment->fetch(PDO::FETCH_DEFAULT);
        $statment->closeCursor();
        return $result;
    }
    public function getAvailableTickets($event_id){
        $query='SELECT COUNT(*) AS "tickets_sold"
        FROM tickets
        JOIN events ON tickets.event_id=events.event_id
        WHERE events.event_id=:event_id LIMIT 1';
        $statment=$this->conn->prepare($query);
        $statment->bindValue(":event_id",$event_id);
        $statment->execute();
        $result=$statment->fetch(PDO::FETCH_DEFAULT);
        $statment->closeCursor();
        return $result;
    }
    public function checkTicketAvailability(){
        include_once("Events_Manager.php");
        $event_manager=new Events_Manager();
        $event=$event_manager->getRecord($_POST["event_id"]);
        $tickets=$this->getAvailableTickets($_POST["event_id"]);

        $max_seats=$event["max_seats"];
        $ticketsSold=$tickets["tickets_sold"];

        $max_tickets=$max_seats-$ticketsSold;
        if($max_tickets>=$_POST["number_of_tickets"]){
            return true;
        }
        else{
            return false;
        }
    }
    public function getAllTicketInformationForEvent($event_id){
        $query='SELECT * FROM tickets 
        JOIN events ON tickets.event_id=events.event_id
        WHERE events.event_id=:event_id AND events.user_id='."'".$_SESSION["user_id"]."'";
        $statment=$this->conn->prepare($query);
        $statment->bindValue(":event_id",$event_id);
        $statment->execute();
        $result=$statment->fetchAll(PDO::FETCH_ASSOC);
        $statment->closeCursor();
        return $result;
    }
    public function InsertTicket(){

        if($_POST["number_of_tickets"]==1){
            try{
                $query="INSERT INTO tickets (event_id,user_id) VALUES(:event_id,:user_id)";
                $statment=$this->conn->prepare($query);
                $statment->bindValue(":event_id",$_POST["event_id"]);
                $statment->bindValue(":user_id",$_SESSION["user_id"]);
                $statment->execute();
            }
            catch(PDOException $e){
                $error["error"]=$e->getMessage();
                return $error;
            }
        }
        else{
            try{
                $query="INSERT INTO ticket_group () VALUES ()";
                $statment=$this->conn->prepare($query);
                $statment->execute();
                $last_group_id=$this->conn->lastInsertId();

                $query="INSERT INTO tickets (event_id,user_id,ticket_group_id) VALUES(:event_id,:user_id,:ticket_group_id),";
                for($i=1;$i<$_POST["number_of_tickets"];$i++){
                    $query=$query."(:event_id,:user_id,:ticket_group_id),";
                }
                $query=substr($query,0,-1);
                $query.";";

                $statment=$this->conn->prepare($query);
                $statment->bindValue(":event_id",$_POST["event_id"]);
                $statment->bindValue(":user_id",$_SESSION["user_id"]);
                $statment->bindValue(":ticket_group_id",$last_group_id);
                $statment->execute();
                return $statment->rowCount();
            }
            catch(PDOException $e){
                $error["error"]=$e->getMessage();
                return $error;
            }
        }
    }
    public function useTicket(){

        $query="SELECT * FROM tickets WHERE event_id=:event_id AND code=:code AND used=0 LIMIT 1";
        $statment=$this->conn->prepare($query);
        $statment->bindValue(":event_id",$_POST["event_id"]);
        $statment->bindValue(":code",$_POST["code"]);
        $statment->execute();
        $result=$statment->fetch(PDO::FETCH_ASSOC);
        if(!empty($result)){
            try{
                $query="UPDATE tickets SET used=1 WHERE event_id=:event_id AND code=:code";
                $statment=$this->conn->prepare($query);
                $statment->bindValue(":event_id",$_POST["event_id"]);
                $statment->bindValue(":code",$_POST["code"]);
                $statment->execute();
                return true;
            }
            catch(PDOException $e){
                return false;
            }
        }
        else{
            return false;
        }

    }


}

?>