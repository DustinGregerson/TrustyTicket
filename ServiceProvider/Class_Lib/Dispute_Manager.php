<?php
    include_once("DB_Access.php");
    class Dispute_Manager{
        private $db;

        private $conn;

        private $error=[];
        public function __construct() {
            $this->db=new DB_Access();
            $this->db->setConnection();
            $this->conn=$this->db->getConnection();
        }
        public function insertDispute(){
            //Does this user own this ticket.
            $query="SELECT user_id FROM tickets WHERE ticket_id=:ticket_id AND user_id=:user_id";
            $statment=$this->conn->prepare($query);
            $statment->bindValue(":ticket_id",$_POST["ticket_id"]);
            $statment->bindValue(":user_id",$_SESSION["user_id"]);
            $statment->execute();
            $result=$statment->fetchAll(PDO::FETCH_ASSOC);
            $statment->closeCursor();
            if($result){
                try{
                    //Does this ticket already have a dispute
                    $query="SELECT ticket_id FROM disputes WHERE ticket_id=:ticket_id";
                    $statment=$this->conn->prepare($query);
                    $statment->bindValue(":ticket_id",$_POST["ticket_id"]);
                    $statment->execute();
                    $result=$statment->fetchAll(PDO::FETCH_ASSOC);
                    $statment->closeCursor();
                    if(!$result){
                        //This ticket is owned by the user and it does not have a dispute.
                        $query="INSERT INTO disputes (ticket_id,reason) VALUES (:ticket_id,:reason)";
                        $statment=$this->conn->prepare($query);
                        $statment->bindValue(":ticket_id",$_POST["ticket_id"]);
                        $statment->bindValue(":reason",$_POST["reason"]);
                        $statment->execute();
                        return true;
                    }
                    else{
                        return false;
                    }

                }
                catch(PDOException $e){
                    //errors go here
                    return $e->getMessage();
                }
            }
            else{
                //This ticket does not exists or the user is attempting to file a dispute for a ticket they do not own
                //There should not be a case where this can happen so the session needs to be destroyed.
                session_destroy();
                //returning true to cause the window to refresh and take the user to the login screen
                return true;
            }
        }
        public function getAllDisputes(){
                            //host          
            $query="SELECT disputes.dispute_id,users.username,events.name,tickets.bought_on,tickets.code,tickets.used,disputes.date_filed,disputes.reason FROM users 
            JOIN events ON users.user_id=events.user_id
            JOIN tickets ON tickets.event_id=events.event_id
            JOIN disputes ON tickets.ticket_id=disputes.ticket_id
            WHERE tickets.user_id=".$_SESSION["user_id"];
            $statment=$this->conn->prepare($query);
            $statment->execute();
            $result=$statment->fetchAll();
            $statment->closeCursor();
            return $result;

        }
        public function isDisputeFiled($ticket_id){
            $query="SELECT ticket_id FROM disputes WHERE ticket_id=:ticket_id";
            $statment=$this->conn->prepare($query);
            $statment->bindValue(":ticket_id",$ticket_id);
            $statment->execute();
            $result=$statment->fetchAll(PDO::FETCH_ASSOC);
            $statment->closeCursor();
            return empty($result);
        }
        public function isDisputeOngoing($dispute_id){
            $query="SELECT * FROM disputes
            JOIN tickets ON tickets.ticket_id=disputes.ticket_id
            JOIN payout ON payout.ticket_id=tickets.ticket_id
            WHERE dispute_id=:dispute_id AND payout.to_host=null";
            $statment=$this->conn->prepare($query);
            $statment->bindValue(":dispute_id",$dispute_id);
            $statment->execute();
            $result=$statment->fetch(PDO::FETCH_ASSOC);
            $statment->closeCursor();
            return empty($result);
        }
        public function settleDispute()
        {
            $query="UPDATE payout SET to_host=:to_host WHERE ticket_id=:ticket_id";
            $statment=$this->conn->prepare($query);
            $statment->bindValue(":to_host",$_POST["to_host"]);
            $statment->bindValue(":ticket_id",$_POST["ticket_id"]);
            $statment->execute();
        }
    }
?>