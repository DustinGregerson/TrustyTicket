<?php

// Step 1: Configure session cookie parameters (optional)
$cookie_lifetime = 604800; // 7 days
$cookie_path = "/";
$cookie_domain = ""; // Default, usually your domain
$cookie_secure = false; // true if you want to send the cookie over HTTPS only
$cookie_httponly = true; // true to make the cookie accessible only through the HTTP protocol

session_set_cookie_params($cookie_lifetime);
session_start();
session_regenerate_id(true);

//prevents session hijacking by checking that the machine that logged into the account is the same machine that
//submitted the sesshion cookie to the server

if (!isset($_SESSION['ip_address'])) {
    $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
} elseif ($_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR']) {
    // Possible session hijacking attempt, destroy the session
    session_destroy();
}

if(isset($_SESSION["username"])){
    if(!isset($_GET["action"])){
        $_GET["action"]="hub";
    }
}

else{
    if(!isset($_GET["action"]))
    $_GET["action"]="login";
}

include("header.php");

switch($_GET["action"]){
    case "hub":
        if(!isset($_GET["sub"])){
            include("Client/hub.php");
        }
        else{
            if($_GET["sub"]=="add_event"){
                include("Client/Event_Insert_Form.html");
            }
            if($_GET["sub"]=="edit_event"){
                include("Client/Event_Update_Form.php");
            }
        }
    break;
    case "login":
        include("Client/login.html");
    break;
    case "register":
        include("Client/register.html");
    break;
    case "logout":
        include_once("ServiceProvider/Class_lib/Account_Manager.php");
        session_destroy();
        //when the website is build this would redirect to the login screen
        header("Location:index.php");
    break;
    case "tickets":
        include_once("Client/Ticket_Purchase_Form.html");
    break;
}
include("footer.php");

include_once("ServiceProvider/Utilities/Tables_Print.php");
include_once("ServiceProvider/Utilities/Tables_Query_Builder.php");
/*
$tablePrinter=new Tables_Print();
$tablePrinter->startForm(true,"ServiceProvider/API.php","POST","update");
$tablePrinter->getTableUpdateInputs("events","0",["times_changed","user_id","event_id","event_code"]);
$tablePrinter->getTableUpdateInputs("pictures","0",["event_id","picture_id"]);
$tablePrinter->endForm("Save");
$tablePrinter->writeFormToFile("test.php");
*/
/*
$queryBuilder=new Tables_Query_Builder();
$queryBuilder->buildUpdateQuery("events","event_id=:event_id",["times_changed","user_id","event_code"]);
$queryBuilder->buildStatmentBinder("events",'$_POST',["times_changed","user_id","event_id","event_code"]);
$queryBuilder->writeToFile("test.php");
*/
?>
