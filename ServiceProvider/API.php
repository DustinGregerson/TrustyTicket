<?php
header("Content-Type: application/json");
session_start();
if(isset($_POST["api_function_call"])){
    

    switch($_POST["api_function_call"]){
        case "login":
            include_once("Class_lib/Account_Manager.php");
            $accountManager=new Account_Manager(); 
            $data=$accountManager->login();
        break;
        case "register":
            include_once("Class_lib/Account_Manager.php");
            $accountManager=new Account_Manager(); 
            $data=$accountManager->register();
        break;
        case "insert_event":
            include_once("Class_lib/Events_Manager.php");
            $event_manager=new Events_Manager();
            $data=$event_manager->insert_event();
        break;
        case "update_event":
            include_once("Class_lib/Events_Manager.php");
            $event_manager=new Events_Manager();
            $data=$event_manager->update_event();
        break;
    }
    print(json_encode($data));
}
else{
    $value=[];
    $value["fail"]="true";
    print(json_encode($value));
}

