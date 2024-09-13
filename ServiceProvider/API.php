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
        case "ban_user":
            include_once("Class_lib/Account_Manager.php");
            $accountManager=new Account_Manager();
            $data=$accountManager->banUser();
        break;
        case "unban_user":
            include_once("Class_lib/Account_Manager.php");
            $accountManager=new Account_Manager();
            $data=$accountManager->unbanUser();
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
        case "rate_event":
            include_once("Class_Lib/Rating_Manager.php");
            $rating_manager=new Rating_Manager();
            $data=$rating_manager->rateEvent();
        break;
        case "buy_ticket":
            include_once("Class_lib/Payment_Manager.php");
            $payment_manager= new Payment_Manager();
            $data=$payment_manager->PaymentIntent();
        break;
        case "check_ticket_availability":
            include_once("Class_lib/Ticket_Manager.php");
            $ticket_Manager=new Ticket_Manager();
            $data=$ticket_Manager->checkTicketAvailability();
        break;
        case "insert_ticket":
            include_once("Class_lib/Ticket_Manager.php");
            $ticket_Manager=new Ticket_Manager();
            $data=$ticket_Manager->InsertTicket();
        break;
        case "use_ticket":
            include_once("Class_lib/Ticket_Manager.php");
            $ticket_Manager=new Ticket_Manager();
            $data=$ticket_Manager->useTicket();
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

