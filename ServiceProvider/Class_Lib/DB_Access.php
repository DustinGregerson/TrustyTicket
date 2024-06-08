<?php

class DB_Access
{
    private static $conn;
	private static $hostName = "localhost";
	private static $databaseName = "trustyticket";
	private static $userName = "root";
	private static $password = "";

    private static $error="error connecting to database";

    function setConnection(...$args){
		if(empty($args)){
			try{
					$dsn = "mysql:host=".self::$hostName.";dbname=".self::$databaseName.";";
					self::$conn = new PDO($dsn,self::$userName,self::$password);
					self::$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
					return null;
				}
				catch (Exception $e){
					echo($e->getMessage());
					return $e;
				}
			}
	}

    
    function getConnection(){
        return self::$conn;
    }
	
	public function showTables()
	{	
		self::setConnection();
		if(self::$conn==null){return self::$error;}

		$query = "SHOW TABLES FROM " . self::$databaseName;
		$statment=self::$conn->prepare($query);
		$statment->execute();
		$result = $statment->fetchAll(PDO::FETCH_ASSOC);
		$statment->closeCursor();
		return $result;

	}
	public function getColumns($tableName){
		self::setConnection();
		if(self::$conn==null){return self::$error;}
		$query = "SHOW COLUMNS FROM ".$tableName;
		$statment=self::$conn->prepare($query);
		$statment->execute();
		$result = $statment->fetchAll(PDO::FETCH_ASSOC);
		$statment->closeCursor();

		$temp=[];
		foreach($result as $row){
			array_push($temp,$row["Field"]);
		}

		return $result;
	}
}