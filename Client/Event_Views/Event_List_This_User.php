<?php
    include_once("serviceProvider/Class_lib/Events_Manager.php");
    include_once("serviceProvider/Class_lib/Picture_Formatter.php");
    $event_manager=new Events_Manager();

    if(isset($_GET["sort"])){
        $events=$event_manager->getSortedEvents();
    }
    else{
        $events=$event_manager->GetAllUserEvents();
    }

?>
        <main class="content">
        <div class="event-sort">
            <a href="?action=events&sort=past">Past Events</a>
            <a href="?action=events&sort=current">Current Events</a>
            <a href="?action=events&sort=future">Future Events</a>
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
                            <?php if($event["average_rating"]):?>
                                <p>Rating: <?php echo($event["average_rating"])?></p>
                            <?php else:?>
                                <p>Rating: 0</p>
                            <?php endif;?>
                            <?php if(strtotime($event["event_start"])>time()):?>
                                <a href="?sub=edit_event&event_id=<?php echo($event["event_id"])?>">Edit</a>
                            <?php endif;?>
                            <a href="?action=events&sub=event_details&event_id=<?php echo($event["event_id"])?>">Event Details</a>
                        </div>
                    </div>
                    <?php endforeach;?>
                <?php else:?>
                    <?php if(isset($_GET["sort"])):?>

                        <?php switch($_GET["sort"]):
                            case "past":?>
                                <div>You do not have past events</div>
                            <?php break;?>
                            <?php case "current":?>
                                <div>You do not have events that are happening right now</div>
                            <?php break;?>
                            <?php case "future":?>
                                <div>You do not have events set to start in the future</div>
                            <?php break;?>
                            <?php default:?>
                            <div>No events to dispay start an event today.</div>
                            <?php break;?>
                            <?php endswitch;?>
                    <?php else:?>
                    <div>No events to dispay start an event today.</div>
                    <?php endif;?>
                <?php endif;?>
            </div>
        </main>