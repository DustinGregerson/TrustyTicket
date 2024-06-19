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
                //Does this ticket already have a dispute
                $query="SELECT ticket_id FROM disputes WHERE ticket_id=:ticket_id";
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
                }   
            }
            else{
                //This ticket does not exists or the user is attempting to file a dispute for a ticket they do not own
                //There should not be case where this can happen so the session needs to be destroyed.
                session_destroy();
            }
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