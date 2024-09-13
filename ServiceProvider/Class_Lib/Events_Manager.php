<?php
include_once("DB_Access.php");
class Events_Manager{

    private $db;

    private $conn;

    public $error=[];

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
            $query="SELECT * 
            FROM events
            JOIN event_dates ON events.event_id = event_dates.event_id
            WHERE events.user_id=:user_id";
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
                $query="SELECT * FROM events
                JOIN event_dates ON events.event_id=event_dates.event_id
                JOIN pictures ON pictures.event_id=events.event_id
                WHERE end_relative_to_central_time<NOW() AND events.user_id=:user_id";
            break;
            case "current":
                $query="SELECT * FROM events
                JOIN event_dates ON events.event_id=event_dates.event_id
                JOIN pictures ON pictures.event_id=events.event_id
                WHERE end_relative_to_central_time>NOW() AND start_relative_to_central_time<NOW() AND events.user_id=:user_id";
            break;
            case "future":
                $query="SELECT * FROM events
                JOIN event_dates ON events.event_id=event_dates.event_id
                JOIN pictures ON pictures.event_id=events.event_id
                WHERE start_relative_to_central_time>NOW() AND events.user_id=:user_id";
            break;
        }
        $statment=$this->conn->prepare($query);
        $statment->bindValue(":user_id",$_SESSION["user_id"]);
        $statment->execute();
        $result=$statment->fetchall(PDO::FETCH_ASSOC);
        $statment->closeCursor();
        return $result;

    }
    public function insert_event(){
        $this->conn->beginTransaction();
        //insert new event
        try{
        $query="INSERT INTO events(user_id,name,event_description,max_seats,charge,show_event,private_event,location,event_category_id) VALUES(:user_id,:name,:event_description,:max_seats,:charge,:show_event,:private_event,:location,:event_category_id)";
        $statment=$this->conn->prepare($query);
        $statment->bindValue(":user_id",$_SESSION["user_id"]);
        $statment->bindValue(":name",htmlspecialchars($_POST["name"]));
        $statment->bindValue(":event_description",htmlspecialchars($_POST["event_description"]));
        $statment->bindValue(":max_seats",$_POST["max_seats"]);
        $statment->bindValue(":charge",$_POST["charge"]);
        $statment->bindValue(":show_event",$_POST["show_event"]);
        $statment->bindValue(":private_event",$_POST["private_event"]);
        $statment->bindValue(":location",$_POST["location"]);
        $statment->bindValue(":event_category_id",$_POST["event_category_id"]);
        $statment->execute();
        $lastId=$this->conn->lastInsertId();
        //insert event dates
        try{
            $query="INSERT INTO event_dates(event_id, start_date , end_date, time_zone) VALUES (:event_id,:start_date,:end_date,:time_zone)";
            $statment=$this->conn->prepare($query);
            $statment->bindValue(":event_id",$lastId);
            $statment->bindValue(":start_date",$_POST["start_date"]);
            $statment->bindValue(":end_date",$_POST["end_date"]);
            $statment->bindValue(":time_zone",htmlspecialchars($_POST["time_zone"]));
            $statment->execute();
            }
            catch(PDOException $e){
                $this->conn->rollBack();
                if($e->getCode()==45000){
                    $this->addToErrorArray("date_fail","The length of the event must be less than 14 days in length");
                }
                else if($e->getCode()==45001){
                    $this->addToErrorArray("date_fail",'Your event can not start before todays date and time. Did you put in the correct time zone?');
                }
                else if($e->getCode()==45009){
                    $this->addToErrorArray("date_fail",'Your event  start date and time can not be before your event end date and time.');
                }
                else{
                    $this->addToErrorArray("date_fail",'Something went wrong plase contact customer support.');
                }
                return $this->error;
            }
        }
        catch(PDOException $e){
            $this->conn->rollBack();
            $this->addToErrorArray("event_insert_fail",$e->getMessage());
            return $this->error;
        }
        $this->conn->commit();

            //row inserted is picture sent?
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
            $query="SELECT * FROM Events
            JOIN Event_dates ON Events.event_id=Event_dates.event_id WHERE events.event_id=:event_id LIMIT 1";
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
        public function update_event(){
        $this->conn->beginTransaction();
           try{
           $query="UPDATE events SET 
           name=:name,
           event_description=:event_description,
           max_seats=:max_seats,
           charge=:charge,
           show_event=:show_event,
           private_event=:private_event,
           event_category_id=:event_category_id
           WHERE event_id=:event_id";
            $statment=$this->conn->prepare($query);
            $statment->bindValue(":event_id",$_POST["event_id"]);
            $statment->bindValue(":name",htmlspecialchars($_POST["name"]));
            $statment->bindValue(":event_description",htmlspecialchars($_POST["event_description"]));
            $statment->bindValue(":max_seats",$_POST["max_seats"]);
            $statment->bindValue(":charge",$_POST["charge"]);
            $statment->bindValue(":show_event",$_POST["show_event"]);
            $statment->bindValue(":private_event",$_POST["private_event"]);
            $statment->bindValue(":event_category_id",$_POST["event_category_id"]);
            $statment->execute();
           }
           catch(PDOException $e){
               $this->conn->rollBack(); 
                return $e->getCode();
           }
           try{
            $query="UPDATE event_dates SET 
            start_date=:start_date,
            end_date=:end_date,
            time_zone=:time_zone
            WHERE event_id=:event_id";
            $statment=$this->conn->prepare($query);
            $statment->bindValue(":event_id",$_POST["event_id"]);
            $statment->bindValue(":start_date",$_POST["start_date"]);
            $statment->bindValue(":end_date",$_POST["end_date"]);
            $statment->bindValue(":time_zone",$_POST["time_zone"]);
            $statment->execute();
           }
           catch(PDOException $e){
                $this->conn->rollBack();
                return $e->getCode();
           }
            try{
                if(isset($_FILES["image"])&& $_FILES["image"]["error"] !== UPLOAD_ERR_NO_FILE){
                    include_once("Picture_Formatter.php");
                    $query="UPDATE pictures SET picture=:picture WHERE event_id=:event_id";
                    $pic=ConvertToBlob();
                    $statment=$this->conn->prepare($query);
                    $statment->bindValue(":event_id",$_POST["event_id"]);
                    $statment->bindValue(":picture",$pic);
                    try{
                        $statment->execute();
                        $this->conn->commit();
                        return true;
                    }
                    catch(PDOException $e){
                        $this->conn->rollBack();
                        return $e->getCode();
                    }
                }
                else{
                    $this->conn->commit();
                    return true;
                }
            }
            catch(PDOException $e){
                $this->conn->rollBack();
                return $e->getCode();
            }

        }
        public function getAllEventsNotCurrentUser(){
            $query="SELECT 
            users.username,
            events.event_id,
            events.user_id,
            events.name,
            event_dates.start_date,
            event_dates.end_date,
            events.charge,
            events.event_description,
            events.private_event,
            events.location,
            pictures.picture,
            events.average_rating,
            event_dates.start_relative_to_central_time,
            event_dates.time_zone
            FROM events
            JOIN event_dates ON events.event_id=event_dates.event_id
            JOIN users ON events.user_id=users.user_id
            LEFT JOIN pictures ON pictures.event_id=events.event_id
            WHERE events.user_id NOT IN (
                SELECT host_id 
                FROM ban_user_table
                WHERE banned_user_id =:user_id
                ) 
            AND ";

            $where="events.user_id !=:user_id AND 
            event_dates.start_relative_to_central_time>now() AND 
            events.show_event=TRUE ";

            $and=true;
            $category=false;
            $username=false;
            $event_code=false;

            if(isset($_GET["category"])&&$_GET["category"]!="None"){
                if($and){$where=$where." AND "; $and=false;}
                $category=true;
                $where=$where."events.event_category_id=:event_category_id AND ";
            }
            if(isset($_GET["username"])){
                if($and){$where=$where." AND "; $and=false;}
                $username=true;
                $where=$where."users.username=:username AND ";
            }
            if(isset($_GET["event_code"])){
                if($and){$where=$where." AND "; $and=false;}
                $event_code=true;
                $where=$where."events.event_code=:event_code AND ";
            }

            if($and){$where=$where." AND "; $and=false;}

            if(isset($_GET["username"])&&isset($_GET["event_code"])){
                $where=$where."events.private_event=true;";
            }
            else{
                $where=$where."events.private_event=false;";
            }

            $query=$query.$where;

            $statment=$this->conn->prepare($query);
            $statment->bindValue(":user_id",$_SESSION["user_id"]);
            if($category){
                $statment->bindValue(":event_category_id",$_GET["category"]);
            }
            if($username){
                $statment->bindValue(":username",$_GET["username"]);
            }
            if($event_code){
                $statment->bindValue(":event_code",$_GET["event_code"]);
            }
            $statment->execute();
            $result=$statment->fetchAll(PDO::FETCH_ASSOC);
            $statment->closeCursor();
            return $result;
        }

        public function isEventOwner($event_id,$user_id){
            $query='SELECT COUNT(*) AS "is_owner"  FROM events WHERE event_id=:event_id AND user_id=:user_id  LIMIT 1';
            $statment=$this->conn->prepare($query);
            $statment->bindValue(":event_id",$event_id);
            $statment->bindValue(":user_id",$user_id);
            $statment->execute();
            $result=$statment->fetch(PDO::FETCH_DEFAULT);
            $statment->closeCursor();
            return $result["is_owner"];
        }

        public function getCategories(){
            $query='SELECT * FROM event_categories';
            $statment=$this->conn->prepare($query);
            $statment->execute();
            $result=$statment->fetchAll(PDO::FETCH_ASSOC);
            $statment->closeCursor();
            return $result;
        }

        private function addToErrorArray($errorName,$errorMessage){
            $this->error[$errorName]=$errorMessage;
        }
}
?>