<?php
    include_once("ServiceProvider/Class_lib/Ticket_Manager.php");
    include_once("ServiceProvider/Class_lib/Events_Manager.php");
    include_once("ServiceProvider/Class_lib/Rating_Manager.php");
    include_once("ServiceProvider/Class_lib/Dispute_Manager.php");

    $ticket_Manager = new Ticket_Manager();
    $event_manager  = new Events_Manager();
    $rating_manager = new Rating_Manager();
    $dispute_manager= new Dispute_Manager();
    if(!isset($_GET["sort"])){
        $tickets=$ticket_Manager->getUserTickets();
    }
    else{
        $tickets=$ticket_Manager->getUserTicketsSorted();
    }
    $count=0;

?>
<div id="Ticket_List">
    <div class="ticket_sort">
        <a class="link dark" href="?action=tickets&sort=past">Past Tickets</a>
        <a class="link dark" href="?action=tickets&sort=ticket_check_in">Tickets Ready To Check-in</a>
        <a class="link dark" href="?action=tickets&sort=future">Future Tickets</a>
    </div>

    <div class="mobile">
        <h1>Your Tickets</h1>  
        <?php foreach($tickets as $ticket): ?>
            <div class="ticket_information">
                <h1>Ticket For</h1>
                <?php
                    $event = $event_manager->getRecord($ticket["event_id"]);
                    // Creates a DateTime object and adds 14 days to the date to for dispute window
                    $disputeWindow = new DateTime($event["end_relative_to_central_time"]);
                    $disputeWindow->add(new DateInterval("P14D"));
                ?>
                <div class="details_item">
                    <div><?php echo($event["name"]) ?></div>
                </div>
                <div class="details_item">
                    <div>Bought On</div>
                    <div><?php echo($ticket["bought_on"]) ?></div>
                </div>
                <div class="details_item">
                    <div>Price</div>
                    <div><?php echo($event["charge"]) ?></div>
                </div>
                <div class="details_item">
                    <div>Used</div>
                    <?php if($ticket["used"]): // was this ticket used?? ?>
                        <div>Yes</div>
                    <?php else: ?>
                        <div>No</div>
                    <?php endif; ?>
                </div>
                <div class="details_item">
                    <div>Code</div>
                    <div><?php echo($ticket["code"]) ?></div>
                </div>
                <div class="details_item">
                    <div>Event Start Date</div>
                    <div><?php echo($event["start_date"]) ?></div>
                </div>
                <div class="details_item">
                    <div>Event End Date</div>
                    <div><?php echo($event["end_date"]) ?></div>
                </div>
                
                <?php if($disputeWindow->getTimestamp() > time() && !$dispute_manager->isDisputeFiled($ticket["ticket_id"])): // is the current time outside of the dispute window?? ?>
                    <div class="details_item">
                        <div>Dispute</div>
                        <div class="button">
                            <button><a href="?action=dispute&sub=file&ticket_id=<?php echo($ticket["ticket_id"]) ?>">yes?</a></button>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if(strtotime($event["start_relative_to_central_time"]) < time()): ?>
                    <div class="details_item">
                        <?php if(!$rating_manager->isEventRated($ticket["ticket_id"])): // Was the event rated for this ticket?? ?>
                            <div>
                                <div>Rate This Event</div>
                                <div class="button_select">
                                    <div>
                                        <button class="button_default" onclick='rateEvent(<?php echo($ticket["ticket_id"]) ?>, <?php echo($event["event_id"]) ?>, this)'><a>Rate event</a></button>
                                    </div>
                                    <select id="Rate_Event_Select_<?php echo($ticket["ticket_id"]); ?>">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                        <option value="8">8</option>
                                        <option value="9">9</option>
                                        <option value="10">10</option>
                                    </select>
                                </div>
                            </div>
                        <?php else: ?>
                            <div>Event Rated</div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="tablet">
        <h1>Your Tickets</h1>  
        <?php foreach($tickets as $ticket): ?>
            <div class="ticket_information">
                <h1>Ticket For</h1>
                <?php
                    $event = $event_manager->getRecord($ticket["event_id"]);
                    // Creates a DateTime object and adds 14 days to the date to for dispute window
                    $disputeWindow = new DateTime($event["end_relative_to_central_time"]);
                    $disputeWindow->add(new DateInterval("P14D"));
                ?>
                <div class="details_item">
                    <div><?php echo($event["name"]) ?></div>
                </div>
                <div class="details_item">
                    <div>Bought On</div>
                    <div><?php echo($ticket["bought_on"]) ?></div>
                </div>
                <div class="details_item">
                    <div>Price</div>
                    <div><?php echo($event["charge"]) ?></div>
                </div>
                <div class="details_item">
                    <div>Used</div>
                    <?php if($ticket["used"]): // was this ticket used?? ?>
                        <div>Yes</div>
                    <?php else: ?>
                        <div>No</div>
                    <?php endif; ?>
                </div>
                <div class="details_item">
                    <div>Code</div>
                    <div><?php echo($ticket["code"]) ?></div>
                </div>
                <div class="details_item">
                    <div>Event Start Date</div>
                    <div><?php echo($event["start_date"]) ?></div>
                </div>
                <div class="details_item">
                    <div>Event End Date</div>
                    <div><?php echo($event["end_date"]) ?></div>
                </div>
                
                <?php if($disputeWindow->getTimestamp() > time() && !$dispute_manager->isDisputeFiled($ticket["ticket_id"])): // is the current time outside of the dispute window?? ?>
                    <div class="details_item">
                        <div>Dispute</div>
                        <div class="button">
                            <button><a href="?action=dispute&sub=file&ticket_id=<?php echo($ticket["ticket_id"]) ?>">yes?</a></button>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if(strtotime($event["start_relative_to_central_time"]) < time()): ?>
                    <div class="details_item">
                        <?php if(!$rating_manager->isEventRated($ticket["ticket_id"])): // Was the event rated for this ticket?? ?>
                            <div>
                                <div>Rate This Event</div>
                                <div class="button_select">
                                    <div>
                                        <button class="button_default" onclick='rateEvent(<?php echo($ticket["ticket_id"]) ?>, <?php echo($event["event_id"]) ?>, this)'><a>Rate event</a></button>
                                    </div>
                                    <select id="Rate_Event_Select_<?php echo($ticket["ticket_id"]); ?>">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                        <option value="8">8</option>
                                        <option value="9">9</option>
                                        <option value="10">10</option>
                                    </select>
                                </div>
                            </div>
                        <?php else: ?>
                            <div>Event Rated</div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="desktop">
        <h1>Your Tickets</h1>  
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
                    <th>Dispute</th>
                    <th>Rate Event</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($tickets as $ticket): ?>
                    <?php $event = $event_manager->getRecord($ticket["event_id"]) ?>
                    <tr>
                        <td><?php echo($event["name"]) ?></td>
                        <td><?php echo($ticket["bought_on"]) ?></td>
                        <td><?php echo($event["charge"]) ?></td>
                        <td><?php echo($ticket["used"] ? "Yes" : "No") ?></td>
                        <td><?php echo($ticket["code"]) ?></td>
                        <td><?php echo($event["start_date"]) ?></td>
                        <td><?php echo($event["end_date"]) ?></td>

                        <?php


                            // Creates a DateTime object and adds 14 days to the date to for dispute window
                            $disputeWindow = new DateTime($event["end_relative_to_central_time"]);
                            $disputeWindow->add(new DateInterval("P14D"));
                        ?>

                        <?php if($disputeWindow->getTimestamp() > time() && $dispute_manager->isDisputeFiled($ticket["ticket_id"])): // is the current time outside of the dispute window?? ?>
                            <td><a class="link dark" href="?action=dispute&sub=file&ticket_id=<?php echo($ticket["ticket_id"]) ?>">Dispute</a></td>
                        <?php else: ?>
                            <td></td>
                        <?php endif; ?>
                        
                        <?php if(strtotime($event["start_relative_to_central_time"]) < time()): ?>
                            <?php if(!$rating_manager->isEventRated($ticket["ticket_id"])): // Was the event rated for this ticket?? ?>
                                <td>
                                    <button class="button_default dark" onclick='rateEvent(<?php echo($ticket["ticket_id"]) ?>, <?php echo($event["event_id"]) ?>, this)'>Rate event</button>
                                    <select id="Rate_Event_Select_<?php echo($ticket["ticket_id"]); ?>">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                        <option value="8">8</option>
                                        <option value="9">9</option>
                                        <option value="10">10</option>
                                    </select>
                                </td>
                            <?php else: ?>
                                <td>Event Rated</td>
                            <?php endif; ?>
                        <?php else: ?>
                            <td></td>
                        <?php endif; ?>
                        
                        <td><a class="link dark" href="?action=events&sub=event_details&event_id=<?php echo($event["event_id"]) ?>">Event Details</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function rateEvent(ticket,event,button){
        var ticket_id=ticket;
        var event_id=event;
        var rating=Number($(button).siblings("select").val());
        var data={
            ticket_id:ticket_id,
            event_id:event_id,
            rating:rating,
            api_function_call:"rate_event"
        }
        $.ajax({
            type: "POST",
            url: 'http://localhost/project/trustyticket/serviceProvider/API.php',
            data: data , 
            dataType: 'html',
            encode: true
        })
        .done(function(data) {
                alert("Thank you for rating the event.");
                window.location.href="http://localhost/project/trustyticket?action=tickets";
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
                console.error('Error:', textStatus, errorThrown);
                console.log(data);
        });

    }
</script>