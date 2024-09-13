<?php
$ticket=$_GET["ticket_id"];
include_once("ServiceProvider/Class_Lib/Dispute_Manager.php");
$dispute_manager=new Dispute_Manager();

?>
<?php if($dispute_manager->isDisputeFiled($ticket)):?>

<div id="Dispute_Form">
    <div>All disputes are handeled on a case by case bases and TrustyTickets staff will work to resolve your dispute as soon as possiable
    </div>
    <form id="target" method="post">
        <input type="hidden" name="api_function_call" value="insert_dispute">
        <input type="hidden" name="ticket_id" value="<?php echo($ticket)?>">
        <div class="label_text_area">
            <label>Reason For Ticket Dispute</label>
            <textarea name="reason" value="Enter your reason to dispute this ticket"></textarea>
        </div>
        <input class="button_default" type="submit" value="Submit">
    </form>
    <?php else:?>
        <div>You have already filed a dispute for this ticket.</div>
    <?php endif;?>
</div>

<script>

    $('#target').submit(function(event) {
        event.preventDefault();
        var type='POST';
        var formData=$(this).serialize();
        var error=$("#error");
        var data={};
        
        $.ajax({
            type: type,
            url: 'http://localhost/project/trustyticket/serviceProvider/API.php',
            data: formData , 
            dataType: 'json',
            encode: true
        })
        .done(function(data) {
            console.log(data);
            var errorValue=false;
            if(data['invalid_login']){
                error.html(data['invalid_login']);
                errorValue=true;
            }
            if(!errorValue){
                window.location.href="http://localhost/project/trustyticket?action=dispute";
            }
    
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
                console.error('Error:', textStatus, errorThrown);
                console.log(data);
        });
    });
</script>