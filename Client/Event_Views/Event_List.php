<?php
include_once("ServiceProvider/Class_Lib/Events_Manager.php");
include_once("serviceProvider/Class_lib/Picture_Formatter.php");
$event_manager=new Events_Manager();
$events=$event_manager->getAllEventsNotCurrentUser();
?>

<main class="content">
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
                                <a href="?action=events&sub=edit_event&event_id=<?php echo($event["event_id"])?>">Edit</a>
                            <?php endif;?>
                            <a href="?action=events&sub=event_details&event_id=<?php echo($event["event_id"])?>">Event Details</a>
                        </div>
                    </div>
                    <?php endforeach;?>
                <?php else:?>
                    <div>No events to dispay when a new event is made by another user they will be shown here.
                    </div>
                <?php endif;?>
            </div>
        </main>