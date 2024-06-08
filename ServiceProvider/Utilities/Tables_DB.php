<?php
include_once("ServiceProvider/Class_Lib/DB_Access.php");
interface CRUD{
    public function insert();
    public function read();
    public function update();
    public function delete();
    public function getAll();
}


class Tables_DB implements CRUD{

    public $db;
    public $conn;

    function __construct() {
        $this->db=new DB_Access();
        $this->db->setConnection();
        $this->conn=$this->db->getConnection();
    }
    //API Function
    public function insert(){
        //post form
        //hidden input type text name table_name
        //the name input must match the column name in the table

        $tableName=$_POST['table_name'];
        $sampleRecord=$this->db->getColumns($tableName);

        $fieldNames=[];
        foreach($sampleRecord as $record){
            array_push($fieldNames,$record["Field"]);
        }

        
        $valueKeys=[];
        $postKeys=array_keys($_POST);
        foreach($fieldNames as $name){

            if($name=="user_id"){
                $_POST["user_id"]=$_SESSION["user_id"];
                array_push($valueKeys,$name);
                continue;
            }

            foreach($postKeys as $postKey){
                if($name==$postKey){
                    array_push($valueKeys,$name);
                    break;
                }
            }
        }

        $query=
        "INSERT INTO ".$tableName."(";
        foreach($valueKeys as $key){
            $query=$query.$key.",";
        }

        $query=substr($query,0,strlen($query)-1);
        $query=$query.") VALUES(";
        foreach($valueKeys as $key){
            $query=$query.":".$key.",";
        }
        $query=substr($query,0,strlen($query)-1);
        $query=$query.");";

        $statment=$this->conn->prepare($query);
        
        foreach($valueKeys as $key){
            $statment->bindValue(":".$key,$_POST[$key]);
        }
        $statment->execute();
        if($statment->rowCount()>0){
            return true;
        }
        else{
            return false;
        }
    }
    //API Function
    public function read(){
        $tableName=$_GET['table_name'];

        $sampleRecord=$this->db->getColumns($tableName);
        $primaryKeyField="";

        foreach($sampleRecord as $record){
            if($record["Key"]=="PRI"){
                $primaryKeyField=$record["Field"];
            }
        }

        $query=
        "SELECT * FROM ".$tableName." WHERE ".$primaryKeyField."=:".$primaryKeyField;
       




        $statment=$this->conn->prepare($query);
        
        $statment->bindValue(":".$primaryKeyField,$_GET[$primaryKeyField]);
        
        $statment->execute();
        $result=$statment->fetch(PDO::FETCH_ASSOC);
        $statment->closeCursor();
        return $result;


    }
    //API Function
    public function update(){
        $tableName=$_POST['table_name'];

        $sampleRecord=$this->db->getColumns($tableName);
        $primaryKeyField="";
        $fieldNames=[];
        foreach($sampleRecord as $record){
            if($record["Key"]=="PRI"){
                $primaryKeyField=$record["Field"];
            }
            else{
                array_push($fieldNames,$record["Field"]);
            }
    }


        $valueKeys=[];
        $postKeys=array_keys($_POST);

        foreach($fieldNames as $name){
            foreach($postKeys as $postKey){
                if($name==$postKey){
                    array_push($valueKeys,$name);
                    break;
                }
            }
        }

        $query=
        "UPDATE ".$tableName." SET ";
        foreach($valueKeys as $key){
            $query=$query.$key."=:".$key.",";
        }

        $query=substr($query,0,strlen($query)-1);
        $query=$query." WHERE ".$primaryKeyField."=:".$primaryKeyField;


        $statment=$this->conn->prepare($query);
        
        foreach($valueKeys as $key){
            $statment->bindValue(":".$key,$_POST[$key]);
        }
        $statment->bindValue(":".$primaryKeyField,$_POST[$primaryKeyField]);

        $statment->execute();
        if($statment->rowCount()>0){
            return true;
        }
        else{
            return false;
        }

    }
    //API Function
    public function delete(){
        if($this->AccessDenied()){
            return;
        }
        $tableName=$_POST['table_name'];

        $sampleRecord=$this->db->getColumns($tableName);
        $primaryKeyField="";
        $fieldNames=[];
        foreach($sampleRecord as $record){
            if($record["Key"]=="PRI"){
                $primaryKeyField=$record["Field"];
            }
            else{
                array_push($fieldNames,$record["Field"]);
            }
        }

        $query=
        "DELETE FROM ".$tableName." WHERE ".$primaryKeyField."=:".$primaryKeyField;

        $statment=$this->conn->prepare($query);
        
        $statment->bindValue(":".$primaryKeyField,$_POST[$primaryKeyField]);

        $statment->execute();
        if($statment->rowCount()>0){
            return true;
        }
        else{
            return false;
        }

    }
    //API Function
    public function getAll(){
        if($this->AccessDenied()){
            return;
        }
        $tableName=$_GET['table_name'];

        

        $query=
        "SELECT * FROM ".$tableName;

        $statment=$this->conn->prepare($query);
        
        $statment->execute();
        $result=$statment->fetchAll(PDO::FETCH_ASSOC);
        $statment->closeCursor();
        return $result;
    }

    private function AccessDenied(){
        if(isset($_POST['table_name'])){
            switch($_POST['table_name']){
                case "users": return true;
                case "payout": return true;
                case "disputs": return true;
                case "tickets": return true;
                default: return false;
            }
        }
        else if(isset($_GET['table_name'])){
            switch($_GET['table_name']){
                case "users": return true;
                case "payout": return true;
                case "disputs": return true;
                case "tickets": return true;
                default: return false;
            }
        }
        else{
            return false;
        }
    }

    private function isOwnerOfEvent(){
        $eventId=$_POST["event_id"];
        $userId=$_SESSION["user_id"];
        
    }
    private function isOwnerOfTicket(){
        $userId=$_SESSION["user_id"];
        $ticketId=$_POST["ticket_id"];

    }
    private function isHostOfEvent(){
        $userId=$_SESSION["user_id"];
        $eventId=$_POST["event_id"];
    }

    public function getAllWhere($tableName,$where){
        $query=
        "SELECT * FROM ".$tableName." WHERE ".$where;

        $statment=$this->conn->prepare($query);
        
        $statment->execute();
        $result=$statment->fetchAll(PDO::FETCH_ASSOC);
        $statment->closeCursor();
        return $result;
    }
    public function getCountWhere($tableName,$where){
        $query=
        "SELECT count(*) FROM ".$tableName." WHERE ".$where;

        $statment=$this->conn->prepare($query);
        
        $statment->execute();
        $result=$statment->fetchAll(PDO::FETCH_NUM);
        $result=$result[0][0];
        $statment->closeCursor();
        return $result;
    }
}

?>