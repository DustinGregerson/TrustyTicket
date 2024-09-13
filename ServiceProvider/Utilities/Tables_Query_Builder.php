

<?php 
//Author Dustin Gregerson
    include_once("ServiceProvider/Class_Lib/DB_Access.php");
    class Tables_Query_Builder{

    private $query;

    private $statmentBinder=[];
    private $db;

    private $conn;

    function __construct() {
        $this->db=new DB_Access();
        $this->db->setConnection();
        $this->conn=$this->db->getConnection();
    }
    function buildInsertQuery($tableName,...$remove){
        $query='$query="';
        $sampleRecord=$this->db->getColumns($tableName);
        $columns=[];
        $remove=$remove[0];
        
        foreach($sampleRecord as $record){
            if(!empty($remove)&&in_array($record['Field'],$remove)){
                continue;
            }
            else{
            array_push($columns,$record["Field"]);
            }
        }

        $query=$query."INSERT INTO ".$tableName."(";
        foreach($columns as $column){
            $query=$query.$column.",";
        }
        $query=substr($query,0,strlen($query)-1);
        $query=$query.") VALUES(";

        foreach($columns as $column){
            $query=$query.":".$column.",";
        }
        $query=substr($query,0,strlen($query)-1);
        $query=$query.')";';
        $this->query=$query;
    }
    function buildStatmentBinder($tableName,$array,...$remove){
        $sampleRecord=$this->db->getColumns($tableName);
        $columns=[];
        $statmentBinder="";
        $remove=$remove[0];
        foreach($sampleRecord as $record){
            if(!empty($remove)&&in_array($record['Field'],$remove)){continue;}
            array_push($columns,$record["Field"]);
        }
        
        foreach($columns as $column){
            array_push($this->statmentBinder,$statmentBinder.'$statment->bindValue(":'.$column.'",htmlspecialchars('.$array.'["'.$column.'"]));');
        }
    }
    function buildUpdateQuery($tableName,$where,...$remove){
        $sampleRecord=$this->db->getColumns($tableName);
        $remove=$remove[0];
        $query="UPDATE TABLE ".$tableName." SET ";

        foreach($sampleRecord as $column){
            if(!empty($remove)&&in_array($column['Field'],$remove)){continue;}
            $query=$query.$column['Field']."=:".$column['Field'].",";
        }
        $query=substr($query,0,strlen($query)-1);
        $query=$query." WHERE ".$where;
        $this->query=$query;
    }
    function writeToFile($fileName){
        $file=fopen($fileName,'w');
        fwrite($file,$this->query."\r\n");
        fwrite($file,'$statment=$this->conn->prepare($query);');
        fwrite($file,"\r\n");
        foreach($this->statmentBinder as $bind){
            fwrite($file,$bind."\r\n");
        }
        fclose($file);
    }
}

?>