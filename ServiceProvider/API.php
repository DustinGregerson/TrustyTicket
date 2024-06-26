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
        case "buy_ticket":
            include_once("Class_lib/Payment_Manager.php");
            $_POST["amount"]=5000;//test
            $payment_manager= new Payment_Manager();
            $data=$payment_manager->PaymentIntent();
        break;
        case "pay_out":
            include_once("Class_lib/Payment_Manager.php");
            $payment_manager= new Payment_Manager();
            $data=$payment_manager->payOut();
        break;
        case "refund":
            include_once("Class_lib/Payment_Manager.php");
            $payment_manager= new Payment_Manager();
            $data=$payment_manager->refund();
        break;
        case "create_financial_account":
            include_once("Class_lib/Payment_Manager.php");
            $payment_manager= new Payment_Manager();
            $data=$payment_manager->createStripeConnectAccount();
        break;
        case "attach_bank_account":
            include_once("Class_lib/Payment_Manager.php");
            $payment_manager= new Payment_Manager();
            $data=$payment_manager->createExternalBankAccountForStripeAccount();
        break;
        case "insert_dispute":
            include_once("Class_lib/Dispute_Manager.php");
            $dispute_manager=new Dispute_Manager();
            $data=$dispute_manager->insertDispute();
        break;
    }
    print(json_encode($data));
}
else{
    $value=[];
    $value["fail"]="true";
    print(json_encode($value));
}

