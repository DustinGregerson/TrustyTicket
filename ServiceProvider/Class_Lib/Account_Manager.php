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
            $query="INSERT INTO users (username,password,email,first_name,last_name,state) values(:username,:password,:email,:first_name,:last_name,:state)";

            $statment=$this->conn->prepare($query);
            $statment->bindValue(":username",$username);
            $statment->bindValue(":password",$password);
            $statment->bindValue(":email",$email);
            $statment->bindValue(":first_name",htmlspecialchars($_POST["first_name"]));
            $statment->bindValue(":last_name",htmlspecialchars($_POST["last_name"]));
            $statment->bindValue(":state",htmlspecialchars($_POST["state"]));
            try{
            $statment->execute();
            }
            catch(PDOException $e){
                if($e->getCode()=="23000"){
                    $this->error["email_exists"]="This email is being used by another account. Please choose another email";
                    return $this->error;
                }
            }
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
            $this->addToErrorArray("username_length","Your username must be greater than 5 characters.");
        }
        if($result){
            $this->addToErrorArray("username_exists","This username already exists. Please choose a different username.");
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

    public function banUser(){
        $query="SELECT * FROM users WHERE username=:username";
        $statment=$this->conn->prepare($query);
        $statment->bindValue(":username",$_POST["username"]);
        $statment->execute();
        $result=$statment->fetch(PDO::FETCH_ASSOC);
        $statment->closeCursor();
        $ban_id=null;
        if($result){
            $ban_id=$result["user_id"];
            $query="SELECT * FROM ban_user_table WHERE host_id=:user_id AND banned_user_id=:ban_id";
            $statment=$this->conn->prepare($query);
            $statment->bindValue(":user_id",$_SESSION["user_id"]);
            $statment->bindValue(":ban_id",$result["user_id"]);
            $statment->execute();
            $result=$statment->fetch(PDO::FETCH_ASSOC);
            $statment->closeCursor();
            if($result){
                $this->addToErrorArray("user_is_banned","You have already banned this user.");
                $statment=$this->conn->prepare($query);
                $statment->execute();
                return $this->error;
            }
            else{
                $query="INSERT INTO ban_user_table(host_id,banned_user_id) VALUES(:user_id,:ban_id)";
                $statment=$this->conn->prepare($query);
                $statment->bindValue(":user_id",$_SESSION["user_id"]);
                $statment->bindValue(":ban_id",$ban_id);
                $statment->execute();
                return true;
            }
        }
        else{
            $this->addToErrorArray("user_does_not_exist","This user was not found.");
            return $this->error;
        }
    }
    //This will need to be changed to the user name and not the user_id this is a not secure
    public function unbanUser(){
        $query="DELETE ban_user_table
        FROM ban_user_table
        JOIN users ON users.user_id = ban_user_table.banned_user_id
        WHERE ban_user_table.host_id = :user_id AND users.username = :banned_user_name";
        $statment=$this->conn->prepare($query);
        $statment->bindValue(":user_id",$_SESSION["user_id"]);
        $statment->bindValue(":banned_user_name",$_POST["banned_user_name"]);
        $statment->execute();
        if($statment->rowCount()){
            return true;
        }
        else{
            return false;
        }

    }
    public function getBannedUsers(){
        $query="SELECT * FROM ban_user_table
                JOIN users ON users.user_id=ban_user_table.banned_user_id
                WHERE ban_user_table.host_id=:user_id";
        $statment=$this->conn->prepare($query);
        $statment->bindValue(":user_id",$_SESSION["user_id"]);
        $statment->execute();
        $result=$statment->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getNotifications(){
        $query="SELECT message FROM notifications WHERE user_id=:user_id";
        $statment=$this->conn->prepare($query);
        $statment->bindValue(":user_id",$_SESSION["user_id"]);
        $statment->execute();
        $result=$statment->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    private function addToErrorArray($errorName,$errorMessage){
        $this->error[$errorName]=$errorMessage;
    }
}


?>