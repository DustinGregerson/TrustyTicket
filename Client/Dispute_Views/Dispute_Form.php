<?php
$ticket=$_GET["ticket_id"];

?>

<div>All disputes are handeled on a case by case bases and TrustyTickets staff will work to resolve your dispute as soon as possiable
</div>
<form action="ServiceProvider/API.php" method="post">
    <input type="hidden" name="api_function_call" value="insert_dispute">
    <input type="hidden" name="ticket_id" value="<?php echo($ticket)?>">
    <label>Reason For Ticket Dispute</label>
    <textarea name="reason" value="Enter your reason to dispute this ticket"></textarea>
    <input type="submit" value="Submit">
</form>