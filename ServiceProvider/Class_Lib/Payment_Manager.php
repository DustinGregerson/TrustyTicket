<?php
    include_once("DB_Access.php");
    require_once('vendor/autoload.php');
    //This is a test key
    \Stripe\Stripe::setApiKey('sk_test_51PHBZMHUca1WAyokRWC4GRaKSYwSdXg4q8nmV95JZBleMJJZhoTFO1iJvo0bGOstU5pxPjMdd0fUfJa0LbtmbjyS00bGOV8UUh');
class Payment_Manager{
    private $db;

    private $conn;

    private $error=[];

    private $testKey;
    public function __construct() {
        $this->db=new DB_Access();
        $this->db->setConnection();
        $this->conn=$this->db->getConnection();
        $this->testKey='sk_test_51PHBZMHUca1WAyokRWC4GRaKSYwSdXg4q8nmV95JZBleMJJZhoTFO1iJvo0bGOstU5pxPjMdd0fUfJa0LbtmbjyS00bGOV8UUh';
    }

    function hasStripeConnectAccount(){
        $query='SELECT COUNT(*) AS "count" FROM stripe_accounts WHERE user_id='.$_SESSION["user_id"];
        $statment=$this->conn->prepare($query);
        $statment->execute();
        $result=$statment->fetch(PDO::FETCH_DEFAULT);
        $statment->closeCursor();
        return $result["count"];
    }
    function hasExternalBankAccountForStripeAccount(){
        $query='SELECT COUNT(*) AS "count"  FROM stripe_accounts 
        JOIN stripe_external_bank_accounts on stripe_accounts.stripe_account_id=stripe_external_bank_accounts.stripe_account_id
        WHERE user_id='.$_SESSION["user_id"];
        $statment=$this->conn->prepare($query);
        $statment->execute();
        $result=$statment->fetch(PDO::FETCH_DEFAULT);
        $statment->closeCursor();
        return $result["count"];
    }
    function hasPayout(){
       $query='SELECT COUNT(*) as "count" FROM payout WHERE host_id='.$_SESSION["user_id"].' AND payed_out_on IS NULL AND to_host=1 LIMIT 1';
       $statment=$this->conn->prepare($query);
       $statment->execute();
       $result=$statment->fetch(PDO::FETCH_DEFAULT);
       $statment->closeCursor();
       return $result["count"];
    }
    function getPayoutTotal(){
        $query='SELECT SUM(events.charge) AS "total" from payout
        JOIN tickets on tickets.ticket_id=payout.ticket_id
        JOIN events on events.event_id=tickets.event_id
        WHERE host_id='.$_SESSION["user_id"].' AND payed_out_on IS NULL AND to_host=1';
        $statment=$this->conn->prepare($query);
        $statment->execute();
        $result=$statment->fetch(PDO::FETCH_DEFAULT);
        $statment->closeCursor();
        return $result["total"];
    }
    function getRefundTotal(){
        $query='SELECT SUM(events.charge) AS "total" from payout
        JOIN tickets on tickets.ticket_id=payout.ticket_id
        JOIN events on events.event_id=tickets.event_id
        WHERE attendant_id='.$_SESSION["user_id"].' AND payed_out_on IS NULL AND to_host=0';
        $statment=$this->conn->prepare($query);
        $statment->execute();
        $result=$statment->fetch(PDO::FETCH_DEFAULT);
        $statment->closeCursor();
        return $result["total"];
    }

    function hasRefund(){
        $query='SELECT COUNT(*) as "count" FROM payout WHERE attendant_id='.$_SESSION["user_id"].' AND payed_out_on IS NULL AND to_host=0 LIMIT 1';
        $statment=$this->conn->prepare($query);
        $statment->execute();
        $result=$statment->fetch(PDO::FETCH_DEFAULT);
        $statment->closeCursor();
        return $result["count"];
    }
    function getAccountNames(){
        $query='SELECT name FROM stripe_accounts 
        JOIN stripe_external_bank_accounts on stripe_accounts.stripe_account_id=stripe_external_bank_accounts.stripe_account_id
        WHERE user_id='.$_SESSION["user_id"];
        $statment=$this->conn->prepare($query);
        $statment->execute();
        $result=$statment->fetchall(PDO::FETCH_ASSOC);
        $statment->closeCursor();
        return $result;
    }
    function getAccounts(){
        $query='SELECT * FROM stripe_accounts 
        JOIN stripe_external_bank_accounts on stripe_accounts.stripe_account_id=stripe_external_bank_accounts.stripe_account_id
        WHERE user_id='.$_SESSION["user_id"];
        $statment=$this->conn->prepare($query);
        $statment->execute();
        $result=$statment->fetchall(PDO::FETCH_ASSOC);
        $statment->closeCursor();
        return $result;
    }
    function getPayoutIds(){
        $query='SELECT payout_id from payout
        JOIN tickets on tickets.ticket_id=payout.ticket_id
        JOIN events on events.event_id=tickets.event_id
        WHERE host_id='.$_SESSION["user_id"].' AND payed_out_on IS NULL AND to_host=1';

        $statment=$this->conn->prepare($query);
        $statment->execute();
        $payoutIds=$statment->fetchAll(PDO::FETCH_ASSOC);
        $statment->closeCursor();
        return $payoutIds;
    }
    function getRefundIds(){
        $query='SELECT payout_id from payout
        JOIN tickets on tickets.ticket_id=payout.ticket_id
        JOIN events on events.event_id=tickets.event_id
        WHERE attendant_id='.$_SESSION["user_id"].' AND payed_out_on IS NULL AND to_host=0';

        $statment=$this->conn->prepare($query);
        $statment->execute();
        $payoutIds=$statment->fetchAll(PDO::FETCH_ASSOC);
        $statment->closeCursor();
        return $payoutIds;
    }

    /* Stripe test payout US bank numbers and return strings
    Routing	    Account	        Type/result
    110000000	000123456789	Payout succeeds.
    110000000	000111111116	Payout fails with a no_account code.
    110000000	000111111113	Payout fails with a account_closed code.
    110000000	000222222227	Payout fails with a insufficient_funds code.
    110000000	000333333335	Payout fails with a debit_not_authorized code.
    110000000	000444444440	Payout fails with a invalid_currency code.
    110000000	000888888883	Payout fails if method is instant. Bank account is not eligible for Instant Payouts.
    */

    /*This function creates a stripe connect account so we can payout to the users bank account from our stripe account
        Each stripe connect account has a one to one relationship with a user
        Each stripe connect account may have one or many bank accounts

        We store the tokens for these accounts in the primary key columns of the
        stripe_accounts and stripe_external_bank_accounts tables

        In summery a user may only account but can have many bank accounts associated with it
    */

    function createStripeConnectAccount(){
        //Test key for api to access our stripe connect client
        $stripe = new \Stripe\StripeClient($this->testKey);

        //checks if there is a stripe connect account associated with this user
        $query="SELECT stripe_account_id FROM stripe_accounts WHERE user_id=".$_SESSION["user_id"];
        $statment=$this->conn->prepare($query);
        $statment->execute();
        $result=$statment->fetch(PDO::FETCH_ASSOC);
        $statment->closeCursor();

        //if there isn't an account then we need to make one
        if(empty($result)){
            //gets the user information needed to make the account
            $query="SELECT email FROM users WHERE user_id=".$_SESSION["user_id"];
            $statment=$this->conn->prepare($query);
            $statment->execute();
            $result=$statment->fetch(PDO::FETCH_ASSOC);
            $statment->closeCursor();

            $userDOB=explode("-",$_POST["dob"]);

            $account = $stripe->accounts->create([
                'type' => 'custom',
                'country' => 'US',
                'email' => $result["email"],
                'business_type' => 'individual',
                'business_profile' => [
                    'url' => 'www.TrustyTicket.com'
                ],
                'individual'=>[
                    'first_name' => $_POST["first_name"],
                    'last_name' => $_POST["last_name"],
                    'dob' => [
                        'day' => $userDOB[2],
                        'month' => $userDOB[1],
                        'year' => $userDOB[0],
                    ],
                    'ssn_last_4' => $_POST["ssn4"],
                    'address' => [
                    'line1' => $_POST["street"],
                    'city' => $_POST["city"],
                    'state' => $_POST["state"],
                    'postal_code' => $_POST["zip"],
                    'country' => 'US',
                    ],
                    'phone' => $_POST["phone"],
                ],
                'capabilities' => [
                    'transfers' => ['requested' => true],
                ],

                'tos_acceptance' => [
                    'date' => time(), 
                    'ip' => $_SERVER['REMOTE_ADDR'], 
                ],
            ]);

            //after the account is created we grab the account id token and store it into our data base
            $query="INSERT INTO stripe_accounts(stripe_account_id,user_id) VALUES (:account_id,:user_id)";
            $statment=$this->conn->prepare($query);
            $statment->bindValue(":account_id",$account->id);
            $statment->bindValue(":user_id",$_SESSION["user_id"]);
            $statment->execute();
            return true;
        }
        return false;
    }

    /*
      this function gets a new bank account token from stripe and creates the relationship between the 
      stripe_account and the stripe_externial_bank_account tables
    */
    function createExternalBankAccountForStripeAccount(){
        $stripe = new \Stripe\StripeClient($this->testKey);

        $query="SELECT stripe_account_id FROM stripe_accounts WHERE user_id=".$_SESSION["user_id"];
        $statment=$this->conn->prepare($query);
        $statment->execute();
        $account=$statment->fetch(PDO::FETCH_ASSOC);
        $statment->closeCursor();

        $query="SELECT * FROM users WHERE user_id=".$_SESSION["user_id"];
        $statment=$this->conn->prepare($query);
        $statment->execute();
        $user=$statment->fetch(PDO::FETCH_ASSOC);
        $statment->closeCursor();
        try{
        $bankAccount=$stripe->accounts->createExternalAccount(
            $account["stripe_account_id"],
            [
            'external_account' => [
                'object' => 'bank_account',
                'country' => 'US',
                'currency' => 'usd',
                'routing_number' => $_POST["routing_number"], 
                'account_number' => $_POST["account_number"],
                'account_holder_name' => $user["first_name"]." ".$user["last_name"],
                'account_holder_type' => 'individual',
            ],
            ]
       );

       $query="INSERT INTO stripe_external_bank_accounts (stripe_external_bank_account_id,stripe_account_id,name) VALUES (:bank_account,:account,:name)";
       $statment=$this->conn->prepare($query);
       $statment->bindValue(":bank_account",$bankAccount->id);
       $statment->bindValue(":account",$account["stripe_account_id"]);
       $statment->bindValue(":name",$_POST["name"]);
       $statment->execute();
       $user=$statment->fetch(PDO::FETCH_ASSOC);
       $statment->closeCursor();
            return true;
        }
        catch(Exception $e){
            echo($e->getMessage());
            return false;
        }
    }

    //Might need to combined the getPayoutTotal function and getPayoutIds function there is a chance that an event could end
    //in between the two querys and that could cause an issue.
    public function payOut(){
        $stripe = new \Stripe\StripeClient('sk_test_51PHBZMHUca1WAyokRWC4GRaKSYwSdXg4q8nmV95JZBleMJJZhoTFO1iJvo0bGOstU5pxPjMdd0fUfJa0LbtmbjyS00bGOV8UUh');

        try{
        $query="SELECT * from
        stripe_accounts
        WHERE user_id=".$_SESSION["user_id"];
        $statment=$this->conn->prepare($query);
        $statment->execute();
        $account=$statment->fetch(PDO::FETCH_ASSOC);
        $statment->closeCursor();

        //gets the total amount that needs to be payed out to the users bank and converts it into cents
        $total=$this->getPayoutTotal();
        $total=$total*100;

        //gets payout_id from every record that is going to be payed out
        $payoutIds=$this->getPayoutIds();


        //binds every id to the where clause with a OR oporator to update the records if the id matches the payout id record
        //that was just payed out

        $whereClause="WHERE";

        foreach($payoutIds as $payoutId){
            $whereClause=$whereClause." payout_id=".$payoutId["payout_id"]." OR ";
        }
            $whereClause=substr($whereClause,0,strlen($whereClause)-3);
            $whereClause=$whereClause."LIMIT ".count($payoutIds);

            $query="UPDATE payout SET payed_out_on=0 ".$whereClause;
            
            $this->conn->beginTransaction();
            $statment=$this->conn->prepare($query);
            $statment->execute();
        }

        catch(PDOException $e){
            echo($e->getMessage());
            return false;
        }

        $bankAccount=$_POST["stripe_external_bank_account_id"];
           try {

            //Transfers the funds from our account to the users stripe connect account we created

            $transfer = \Stripe\Transfer::create([
                'amount' => $total, // Amount in cents
                'currency' => 'usd',
                'destination' => $account["stripe_account_id"], // Connected account ID
            ]);


            // Creates a payout to the external account associated with the connected account
            $payout = $stripe->payouts->create([
                'amount' => $total, // Amount in cents
                'currency' => 'usd',
                'destination' => $bankAccount,
            ], [
                'stripe_account' => $account["stripe_account_id"], // Specify the connected account ID
            ]);

            $this->conn->commit();
            return true;
        } catch (\Stripe\Exception\ApiErrorException $e) {

            echo($e->getMessage());
            $this->conn->rollBack();
            return false;
        }
        
    }
    public function refund(){
        $stripe = new \Stripe\StripeClient('sk_test_51PHBZMHUca1WAyokRWC4GRaKSYwSdXg4q8nmV95JZBleMJJZhoTFO1iJvo0bGOstU5pxPjMdd0fUfJa0LbtmbjyS00bGOV8UUh');

        try{
        $query="SELECT * from
        stripe_accounts
        WHERE user_id=".$_SESSION["user_id"];
        $statment=$this->conn->prepare($query);
        $statment->execute();
        $account=$statment->fetch(PDO::FETCH_ASSOC);
        $statment->closeCursor();

        //gets the total amount that needs to be payed out to the users bank and converts it into cents
        $total=$this->getRefundTotal();
        $total=$total*100;

        //gets payout_id from every record that is going to be payed out
        $payoutIds=$this->getRefundIds();


        //binds every id to the where clause with a OR oporator to update the records if the id matches the payout id record
        //that was just payed out

        $whereClause="WHERE";

        foreach($payoutIds as $payoutId){
            $whereClause=$whereClause." payout_id=".$payoutId["payout_id"]." OR ";
        }
            $whereClause=substr($whereClause,0,strlen($whereClause)-3);
            $whereClause=$whereClause."LIMIT ".count($payoutIds);
            //the payed_out_on field is set to 0 because the database trigger is going to set the date of the payout
            $query="UPDATE payout SET payed_out_on=0 ".$whereClause;
            $statment=$this->conn->prepare($query);
            $statment->execute();
        }

        catch(PDOException $e){
            echo($e->getMessage());
            return false;
        }

        $bankAccount=$_POST["stripe_external_bank_account_id"];
           try {

            //Transfers the funds from our account to the users stripe connect account we created

            $transfer = \Stripe\Transfer::create([
                'amount' => $total, // Amount in cents
                'currency' => 'usd',
                'destination' => $account["stripe_account_id"], // Connected account ID
            ]);


            // Creates a payout to the external account associated with the connected account
            $payout = $stripe->payouts->create([
                'amount' => $total, // Amount in cents
                'currency' => 'usd',
                'destination' => $bankAccount,
            ], [
                'stripe_account' => $account["stripe_account_id"], // Specify the connected account ID
            ]);


            return true;
        } catch (\Stripe\Exception\ApiErrorException $e) {

            echo($e->getMessage());
            return false;
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