<?php
include_once("ServiceProvider/Class_Lib/Payment_Manager.php");
$payment_manager=new Payment_Manager();
$accounts=$payment_manager->getAccountNames();

?>

<?php if(!$payment_manager->hasStripeConnectAccount()): //If the user does not have a stripe account token in our database?>
<div>
    When you host an event or receive a refund, you will need to enter your bank account information
    to receive your payout or refund. We do not keep your account information on our server.
    Stripe will handle everything involving your personal information on your Stripe Connect account,
    which will be made automatically by TrustyTicket, and no further actions will be required from you
    to start receiving payouts and refunds. However, if TrustyTicket needs more information to keep your
    Stripe account working and active, we will ask for it and provide it to Stripe. We can also provide you with
    more information regarding your Stripe Connect account with us if you ask for it.
</div>
<div>Your financial account with us has not been created.</div>
<a href="?action=financial&sub=create_financial_account"><button>Create financial account</button></a>
<?php else:?>

    <?php if($payment_manager->hasExternalBankAccountForStripeAccount()):?>
            <div>Your accounts</div>
            <?php foreach($accounts as $account)://Lists the account names that the user has made?>
                <div>Account name: <?php echo $account["name"]?></div>
            <?php endforeach?>
            <?php if($payment_manager->hasPayout())://If there is a payout in the database?>
                <div>You have a payout pending in the amount of $<?php echo $payment_manager->getPayoutTotal()?></div>
                <a href="?action=financial&sub=payout"><button>initialize payout</button></a>
            <?php else:?>
                <div>You do not have payouts pending</div>
            <?php endif;?>

            <?php if($payment_manager->hasRefund())://If there is a refund in the database?>
                <div>You have a refund pending</div>
                <a href="?action=financial&sub=refund"><button>initialize refund</button></a>
            <?php else:?>
                <div>You do not have a refund pending</div>
            <?php endif;?>
    <?php else:?>

        <div>You do not have a bank account attached to your financial account</div>
        <a href="?action=financial&sub=attach_bank_account"><button>Attach bank account</button></a>
    <?php endif;?>

<?php endif;?>