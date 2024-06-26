<?php
include_once("DB_Access.php");
class Events_Manager{

    private $db;

    private $conn;
    function __construct() {
        $this->db=new DB_Access();
        $this->db->setConnection();
        $this->conn=$this->db->getConnection();
    }

    public function GetAllUserEvents(){
        $query="SELECT user_id FROM users WHERE username=:username";
        $statment=$this->conn->prepare($query);
        $statment->bindValue(":username",$_SESSION["username"]);
        $statment->execute();
        $result=$statment->fetchAll(PDO::FETCH_ASSOC);
        $result=$result[0];
        $statment->closeCursor();
        
        if(!empty($result)){
            $user_id=$result['user_id'];
            $query="SELECT * FROM events WHERE events.user_id=:user_id";
            $statment=$this->conn->prepare($query);
            $statment->bindValue(":user_id",$user_id);
            $statment->execute();
            $result=$statment->fetchAll(PDO::FETCH_ASSOC);
            $statment->closeCursor();

            $eventsTotal=count($result);
            for($i=0;$i<$eventsTotal;$i++){
                $query="SELECT * FROM pictures WHERE pictures.event_id=:event_id LIMIT 1";
                $statment=$this->conn->prepare($query);
                $statment->bindValue(":event_id",$result[$i]["event_id"]);
                $statment->execute();
                $pics=$statment->fetchAll(PDO::FETCH_ASSOC);
                if(!empty($pics)){
                    $pics=$pics[0]["picture"];
                    $result[$i]["picture"]=$pics;
                }
                else{
                    $result[$i]["picture"]=0;
                }
            }

        }
        else{
            //There is no reason the id of the user should not be found from the username so the session needs to be destroyed
            session_destroy();
            header("Location:index.php");
        }

        return $result;
    }
    public function getSortedEvents(){
        switch($_GET["sort"]){
            case "past":
                $query="SELECT events.event_id,events.name,events.event_start,events.event_end,events.charge,events.event_description,pictures.picture,events.average_rating 
                FROM events
                LEFT JOIN pictures ON pictures.event_id=events.event_id
                WHERE event_end<now() AND events.user_id=".$_SESSION["user_id"];
            break;
            case "current":
                $query="SELECT events.event_id,events.name,events.event_start,events.event_end,events.charge,events.event_description,pictures.picture,events.average_rating
                FROM events
                LEFT JOIN pictures ON pictures.event_id=events.event_id
                WHERE event_start<now() AND event_end>now() AND events.user_id=".$_SESSION["user_id"];
            break;
            case "future":
                $query="SELECT events.event_id,events.name,events.event_start,events.event_end,events.charge,events.event_description,pictures.picture,events.average_rating
                FROM events
                LEFT JOIN pictures ON pictures.event_id=events.event_id
                WHERE event_start>now() AND events.user_id=".$_SESSION["user_id"];
            break;
        }
        $statment=$this->conn->prepare($query);
        $statment->execute();
        $result=$statment->fetchall(PDO::FETCH_ASSOC);
        $statment->closeCursor();
        return $result;

    }
    public function insert_event(){
        $query="INSERT INTO events(user_id,name,event_description,max_seats,event_start,event_end,charge,show_event,private_event) VALUES(:user_id,:name,:event_description,:max_seats,:event_start,:event_end,:charge,:show_event,:private_event)";
        $statment=$this->conn->prepare($query);
        $statment->bindValue(":user_id",$_SESSION["user_id"]);
        $statment->bindValue(":name",htmlspecialchars($_POST["name"]));
        $statment->bindValue(":event_description",htmlspecialchars($_POST["event_description"]));
        $statment->bindValue(":max_seats",$_POST["max_seats"]);
        $statment->bindValue(":event_start",$_POST["event_start"]);
        $statment->bindValue(":event_end",$_POST["event_end"]);
        $statment->bindValue(":charge",$_POST["charge"]);
        $statment->bindValue(":show_event",$_POST["show_event"]);
        $statment->bindValue(":private_event",$_POST["private_event"]);
        $statment->execute();
        $lastId=$this->conn->lastInsertId();
            //row inserted
            if($statment->rowCount()>0){
                if(isset($_FILES["image"])){
                include_once("Picture_Formatter.php");
                $pic=ConvertToBlob();
                $query="INSERT INTO pictures(event_id,picture) VALUES(:event_id,:picture)";
                $statment=$this->conn->prepare($query);
                $statment->bindValue(":event_id",$lastId);
                $statment->bindValue(":picture",$pic);
                $statment->execute();
                    if($statment->rowCount()>0){
                        return true;
                    }
                    else{
                        return false;
                    }
                }
                else{
                    return true;
                }
            }
            else{
                return false;
            }
        }
        public function getRecord(...$event_id){
            $query="SELECT * FROM Events where event_id=:event_id";
            $statment=$this->conn->prepare($query);

            if(empty($event_id)){
                $statment->bindValue(":event_id",$_GET["event_id"]);
                $statment->execute();
                $event=$statment->fetchAll(PDO::FETCH_ASSOC);
                $event=$event[0];
                $statment->closeCursor();
                $query="SELECT * FROM pictures WHERE pictures.event_id=:event_id LIMIT 1";
                $statment=$this->conn->prepare($query);
                $statment->bindValue(":event_id",$event["event_id"]);
                $statment->execute();

                $pic=$statment->fetchAll(PDO::FETCH_ASSOC);
                if(!empty($pic)){
                    $pic=$pic[0];
                    $event["picture_id"]=$pic["picture_id"];
                    $event["picture"]=$pic["picture"];
                }
                $statment->closeCursor();
                
                return $event;
            }
            else{
                $event_id=$event_id[0];
                $statment->bindValue(":event_id",$event_id);
                $statment->execute();
                $event=$statment->fetchAll(PDO::FETCH_ASSOC);
                $event=$event[0];
                $statment->closeCursor();
                return $event;
            }
        }
        public function getTicketCount(){
            $query="SELECT count(*) FROM Tickets WHERE event_id=:event_id";
            $statment=$this->conn->prepare($query);
            $statment->bindValue(":event_id",$_GET["event_id"]);
            $statment->execute();
            $ticketsBought=$statment->fetchAll(PDO::FETCH_ASSOC);
            $ticketsBought=$ticketsBought[0];
            $statment->closeCursor();
            return $ticketsBought;
        }
        public function update_event(){
           $query="UPDATE events SET name=:name,event_description=:event_description,max_seats=:max_seats,event_start=:event_start,event_end=:event_end,charge=:charge,show_event=:show_event,private_event=:private_event WHERE event_id=:event_id";
            $statment=$this->conn->prepare($query);
            $statment->bindValue(":event_id",$_POST["event_id"]);
            $statment->bindValue(":name",htmlspecialchars($_POST["name"]));
            $statment->bindValue(":event_description",htmlspecialchars($_POST["event_description"]));
            $statment->bindValue(":max_seats",$_POST["max_seats"]);
            $statment->bindValue(":event_start",$_POST["event_start"]);
            $statment->bindValue(":event_end",$_POST["event_end"]);
            $statment->bindValue(":charge",$_POST["charge"]);
            $statment->bindValue(":show_event",$_POST["show_event"]);
            $statment->bindValue(":private_event",$_POST["private_event"]);
            try{
                $statment->execute();
                if(isset($_FILES["image"])&& $_FILES["image"]["error"] !== UPLOAD_ERR_NO_FILE){
                    include_once("Picture_Formatter.php");
                    $query="UPDATE pictures SET picture=:picture WHERE event_id=:event_id";
                    $pic=ConvertToBlob();
                    $statment=$this->conn->prepare($query);
                    $statment->bindValue(":event_id",$_POST["event_id"]);
                    $statment->bindValue(":picture",$pic);
                    try{
                        $statment->execute();
                        return true;
                    }
                    catch(PDOException $e){
                        return $e->getCode();
                    }
                }
                else{
                    return true;
                }
            }
            catch(PDOException $e){
                return $e->getCode();
            }

        }
        public function getAllEventsNotCurrentUser(){
            $query="SELECT events.event_id,events.name,events.event_start,events.event_end,events.charge,events.event_description,pictures.picture,events.average_rating FROM events
            LEFT JOIN pictures ON pictures.event_id=events.event_id
            WHERE user_id !=".$_SESSION["user_id"]." AND events.event_start>now()";
            $statment=$this->conn->prepare($query);
            $statment->execute();
            $result=$statment->fetchall(PDO::FETCH_ASSOC);
            $statment->closeCursor();
            return $result;
        }
}
?>