<?php
    include_once("ServiceProvider/Class_lib/Ticket_Manager.php");
    include_once("ServiceProvider/Class_lib/Events_Manager.php");
    include_once("ServiceProvider/Class_lib/Rating_Manager.php");
    include_once("ServiceProvider/Class_lib/Dispute_Manager.php");

    $ticket_Manager = new Ticket_Manager();
    $tickets=$ticket_Manager->getUserTickets();

    $event_manager  = new Events_Manager();
    $rating_manager = new Rating_Manager();
    $dispute_manager= new Dispute_Manager();
?>


<table>
    <thead>
        <tr>
            <th>Event Name</th>
            <th>Bought On</th>
            <th>Price</th>
            <th>Used</th>
            <th>Code</th>
            <th>Event Start Date</th>
            <th>Event End Date</th>
        </tr>
</thead>

<?php foreach($tickets as $ticket):?>
    <?php $event=$event_manager->getRecord($ticket["event_id"])?>
    <tr>
        <td><?php echo($event["name"])?></td>
        <td><?php echo($ticket["bought_on"])?></td>
        <td><?php echo($event["charge"])?></td>
        <?php if($ticket["used"])://was this ticket used??>
            <td>Yes</td>
        <?php else:?>
            <td>No</td>
        <?php endif;?>

        <td><?php echo($ticket["code"])?></td>
        <td><?php echo($event["event_start"])?></td>
        <td><?php echo($event["event_end"])?></td>

        <?php if(strtotime($event["event_start"])<time()):?>
            <?php if(!$rating_manager->isEventRated($ticket["ticket_id"]))://Was the event rated for this ticket??>
            <td>
                <button><a>Rate event</a></button>
            </td>
            <?php else:?>
                <td>
                Event Rated
                </td>
            <?php endif;?>
            
           <?php if(!$rating_manager->isHostRated($ticket["ticket_id"]))://Was the host rated for this ticket??> 
            <td>
                <button><a>Rate host</a></button>
            </td>
            <?php else:?>
                <td>
                    Host Rated
                </td>
            <?php endif;?>
                <?php
                    //Creates a DateTime object and adds 14 days to the date to for dispute window
                    $disputeWindow= new DateTime($event["event_end"]);
                    $disputeWindow->add(new DateInterval("P14D"));
                ?>
                
                <?php if($disputeWindow->getTimestamp()>time()&&$dispute_manager->isDisputeFiled($ticket["ticket_id"]))://is the current time outside of the dispute window??>
                    <td>
                        <button><a href="?action=dispute&sub=file&ticket_id=<?php echo($ticket["ticket_id"])?>">Dispute</a></button>
                    </td>
                <?php endif;?>
        <?php endif;?>
        <td>
            <button><a href="?action=event_details&event_id=<?php echo($event["event_id"])?>">Event Details</a></button>
        </td>
    </tr>
<?php endforeach;?>
</table>