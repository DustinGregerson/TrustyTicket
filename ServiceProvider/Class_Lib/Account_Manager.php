<?php
include_once("DB_Access.php");
class Account_Manager{
public $db;
public $conn;

public $error=[];
    function __construct() {
        $this->db=new DB_Access();
        $this->db->setConnection();
        $this->conn=$this->db->getConnection();
    }

    public function login(){
        //user is is attempting to login before the next attempt time 
        if(isset($_SESSION["next_attempt_time"])&&$_SESSION["next_attempt_time"]>time()){
            $waitTime=$_SESSION["next_attempt_time"] - time();
            $waitTime=ceil($waitTime / 60);
            $this->error["invalid_login"]="You have attempted to login to many times. You can try again in ".$waitTime." minutes.";
            return $this->error;
        }

        $password=$_POST["password"];
        $username=$_POST["username"];
        $query="SELECT * FROM users WHERE username=:username";
        $statment=$this->conn->prepare($query);
        $statment->bindValue(":username",$username);
        $statment->execute();
        $result=$statment->fetch(PDO::FETCH_ASSOC);
        $statment->closeCursor();
        
        //does the user name and hashed password match the database record
        if(!empty($result)&&password_verify($password,$result["password"])){
            $_SESSION["username"]=$result["username"];
            $_SESSION["user_id"]=$result["user_id"];
            if(isset($_SESSION["login_attempts"])){
                $_SESSION["login_attempts"]=0;
            }
           
            return true;
        }
        else{
            //Does the current session have a failed login.
            if(!isset($_SESSION["login_attempts"])){
                $_SESSION["login_attempts"]=1;
                $this->error["invalid_login"]="Invalid username or password ";
            }
            //The user has failed to login again, sequential attempts with failed logins after three attempts result in wait
            //times equal to the number of attempts times 3 minutes
            else{
                $_SESSION["login_attempts"]=$_SESSION["login_attempts"]+1;
                if($_SESSION["login_attempts"]>3){
                    $waitTime=($_SESSION["login_attempts"]-3)*3;
                    $_SESSION["next_attempt_time"] = strtotime('+'.$waitTime.' minutes', time());
                    $this->error["invalid_login"]="wrong username or password. You have attempted to login to many times".
                    "Your wait time for the next login is "+$waitTime+" minutes.";
                }
                else{
                    $this->error["invalid_login"]="Invalid username or password ";
                }

            }
            return $this->error;
        }
    }
    public function register(){
        $password=$_POST["password"];
        $username=$_POST["username"];
        $email=$_POST["email"];

        $this->isValidPassword($password);
        $this->isValidUserName($username);
        $this->isValidEmail($email);

        if(empty($this->error)){
            $password=password_hash($password,PASSWORD_DEFAULT);

            $query="INSERT INTO users (username,password,email) values(:username,:password,:email)";

            $statment=$this->conn->prepare($query);
            $statment->bindValue(":username",$username);
            $statment->bindValue(":password",$password);
            $statment->bindValue(":email",$email);
            $statment->execute();
            if($statment->rowCount()>0){
                $_SESSION["username"]=$username;
                $query="SELECT * FROM users WHERE username=:username";
                $statment=$this->conn->prepare($query);
                $statment->bindValue(":username",$username);
                $statment->execute();
                $result=$statment->fetch(PDO::FETCH_ASSOC);
                $statment->closeCursor();
                $_SESSION["user_id"]=$result["user_id"];
                return true;
            }
            else{
                return false;
            }
        }
        else{
            return $this->error;
        }

    }
    public function isValidPassword($password){
        $capLetter="/[A-Z]/";
        $number="/[\d]/";
        $specialChar="/[!@\$#%^\&]/";
        $length=strlen($password);
        if($length<8){
            $this->addToErrorArray("password_length","Password must be at least 8 characters in length");
        }
        if(!preg_match($capLetter,$password)){
            $this->addToErrorArray("password_cap_letter","Password must contain a capital letter");
        }
        if(!preg_match($number,$password)){
            $this->addToErrorArray("password_number","Password must contain a number");
        }
        if(!preg_match($specialChar,$password)){
            $this->addToErrorArray("password_special_char","Password must contain one of these characters (!,@,$,#,%,^,&).");
        }
    }
    public function isValidUserName($userName){
        $query="SELECT * FROM users WHERE username=:username";
        $statment=$this->conn->prepare($query);
        $statment->bindValue(":username",$userName);
        $statment->execute();

        $result=$statment->fetch(PDO::FETCH_ASSOC);
        $statment->closeCursor();
        if(strlen($userName)<6){
            $this->addToErrorArray("username_length","Your username must be greater than 5 characters");
        }
        if($result){
            $this->addToErrorArray("username_exists","That username already exists. Please choose a diffrent username");
        }
        else{
            return false;
        }
    }

    public function isValidEmail($email){
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->addToErrorArray("email_invalid","Please enter a valid email");
        }
    }
    private function addToErrorArray($errorName,$errorMessage){
        $this->error[$errorName]=$errorMessage;
    }
}


?>