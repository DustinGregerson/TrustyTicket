<?php
    include_once("DB_Access.php");
    require_once('../vendor/autoload.php');
    //This is a test key
    \Stripe\Stripe::setApiKey('sk_test_51PHBZMHUca1WAyokRWC4GRaKSYwSdXg4q8nmV95JZBleMJJZhoTFO1iJvo0bGOstU5pxPjMdd0fUfJa0LbtmbjyS00bGOV8UUh');
class Payment_Manager{
    private $db;

    private $conn;

    private $error=[];
    public function __construct() {
        $this->db=new DB_Access();
        $this->db->setConnection();
        $this->conn=$this->db->getConnection();
    }

    public function payOut($destination){
    try {

            $query="SELECT SUM() tickets (event_id,user_id) VALUES(:event_id,:user_id)";
            $statment=$this->conn->prepare($query);
            $statment->bindValue(":event_id",$_POST["event_id"]);
            $statment->bindValue(":user_id",$_SESSION["user_id"]);
            $statment->execute();
            // Create a payout
            $stripe = new \Stripe\StripeClient('sk_test_4eC39HqLyjWDarjtT1zdp7dc');
            $stripe->payouts->create([
            'amount' => 1100,
            'currency' => 'usd',
            "destination"=> $destination,
            ]);
        
            // Handle successful payout
            echo "Payout successful: ";
        
        } catch (Exception $e) {
            // Handle errors
            echo "Error: " . $e->getMessage();
        }
    }
    public function PaymentIntent(){
        
        $amount=$_POST["amount"];
        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => $amount, // amount in cents, $10.99
            'currency' => 'usd',
            ]);
        return ['clientSecret' => $paymentIntent->client_secret];
    }
    public function PurchaseTicket(){
        
        try{
             $query="INSERT INTO tickets (event_id,user_id) VALUES(:event_id,:user_id)";
             $statment=$this->conn->prepare($query);
             $statment->bindValue(":event_id",$_POST["event_id"]);
             $statment->bindValue(":user_id",$_SESSION["user_id"]);
             $statment->execute();
        }
        catch(PDOException $e){
            echo($e->getMessage());
            echo($e->getCode());
            return $e;
        }
    }
}


?>