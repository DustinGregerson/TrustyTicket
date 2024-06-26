<?php

//Configure session cookie parameters (optional)
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

include("header.php");

if(isset($_SESSION["username"])){

    if(!isset($_GET["action"])){
        $_GET["action"]="hub";
    }

    switch($_GET["action"]){
        case "hub":
            if(!isset($_GET["sub"])){
                include("Client/Hub.php");
            }
        break;
        //------------------Events Section---------------------------
        case "events":
            if(!isset($_GET["sub"])){
                include("Client/Event_Views/Event_List_This_User.php");
            }
            else{
                if($_GET["sub"]=="find_events"){
                    include("Client/Event_Views/Event_List.php");
                }
                if($_GET["sub"]=="add_event"){
                    include("Client/Event_Views/Event_Insert_Form.html");
                }
                if($_GET["sub"]=="edit_event"){
                    include("Client/Event_Views/Event_Update_Form.php");
                }
                if($_GET["sub"]=="event_details"){
                    include_once("Client/Event_Views/Event_Details.php");
                }
            }
        break;

        case "logout":
            include_once("ServiceProvider/Class_lib/Account_Manager.php");
            session_destroy();
            //when the website is hosted this would redirect to the login screen
            header("Location:index.php");
        break;
        //------------------Tickets Section----------------------------
        case "tickets":
            include_once("Client/Ticket_Views/Tickets.php");
        break;

        case "buy_tickets":
            include_once("Client/Ticket_Views/Ticket_Purchase_Form.html");
        break;
        //------------------Financial Section----------------------------
        case "financial":
            if(!isset($_GET["sub"])){
                include_once("Client/Financial_Views/Financial.php");
            }
            elseif($_GET["sub"]=="create_financial_account"){
                include_once("Client/Financial_Views/Create_Financial_Account.html");
            }
            else if($_GET["sub"]=="attach_bank_account"){
                include_once("Client/Financial_Views/Add_External_Bank_Account.html");
            }
            else if($_GET["sub"]=="payout"){
                include_once("Client/Financial_Views/Payout_Form.php");
            }
            else if($_GET["sub"]=="refund"){
                include_once("Client/Financial_Views/Refund_Form.php");
            }
        break;
        //------------------Dispute Section----------------------------
        case "dispute":
            if(!isset($_GET["sub"])){
                include_once("Client/Dispute_Views/Disputes.php");
            }
            else{
                if($_GET["sub"]=="file"&&isset($_GET["ticket_id"])){
                    include_once("Client/Dispute_Views/Dispute_Form.php");
                }
                else{
                    include_once("Client/Ticket_Views/Tickets.php");
                }
            }
        break;
    }
}
    //------------------User Account Section----------------------------
else{
    if(!isset($_GET["action"])){
        include("Client/Account_Views/login.html");
    }
    else if($_GET["action"]=="register"){
        include("Client/Account_Views/register.html");
    }
    else{
        include("Client/Account_Views/login.html");
    }
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
