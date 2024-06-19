<?php
    include_once("serviceProvider/Class_lib/Events_Manager.php");
    include_once("serviceProvider/Class_lib/Picture_Formatter.php");
    $event_manager=new Events_Manager();
    $events=$event_manager->GetAllUserEvents();
?>
<a href="ServiceProvider/Class_Lib/Ticket_Manager.php">Test Link</a>
        <main class="content">
        <div class="event-sort">
            <a href="events.php?sort=past">Past Events</a>
            <a href="events.php?sort=current">Current Events</a>
            <a href="events.php?sort=future">Future Events</a>
        </div>
                <?php if(!empty($events)):?>
                    <?php foreach ($events as $event):?>
                    <div class="event">
                        <div class="event-picture">
                            <?php if($event["picture"]):?>
                                <img src="<?php echo(ConvertToImgString($event["picture"]))?>">
                            <?php else:?>
                                No Picture Found.
                            <?php endif;?>
                        </div>
                        <div class="event-details">
                            <h2><?php echo($event["name"]);?></h2>
                            <p>$<?php echo($event["charge"]);?></p>
                            <p><?php echo($event["event_start"]);?> - <?php echo($event["event_end"]);?></p>
                            <p><?php echo($event["event_description"]);?></p>
                            <?php if(strtotime($event["event_start"])>time()):?>
                                <a href="?sub=edit_event&event_id=<?php echo($event["event_id"])?>">Edit</a>
                            <?php endif;?>
                        </div>
                    </div>
                    <?php endforeach;?>
                <?php else:?>
                    <div>You currently do not have any events.
                    </div>
                <?php endif;?>
            </div>
        </main>
