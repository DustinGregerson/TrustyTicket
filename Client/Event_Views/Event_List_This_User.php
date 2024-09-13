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
<div id="Events_Manager">
    <div class="mobile">
        <h1>Manage Your Events</h1>
        <div class="event-sort">
            <a href="?action=events&sort=past">Past Events</a>
            <a href="?action=events&sort=current">Current Events</a>
            <a href="?action=events&sort=future">Future Events</a>
        </div>
        <div>
            <?php if(!empty($events)):?>
                <?php foreach ($events as $event):?>
                <div class="event">
                    <div class="event_picture">
                        <?php if($event["picture"]):?>
                            <img src="<?php echo(ConvertToImgString($event["picture"]))?>">
                        <?php else:?>
                            No Picture Found.
                        <?php endif;?>
                    </div>
                    <div class="event_details">
                        <div class="details_item">
                            <div>Event</div>
                            <div> <?php echo($event["name"]);?></div>
                        </div>
                        <div class="details_item">
                            <div>Charging $ </div>
                            <div><?php echo($event["charge"]);?></div>
                        </div>
                        <div class="details_item">
                            <div>Start Date And Time</div>
                            <div><?php echo($event["start_date"]);?></div>
                        </div>
                        <div class="details_item">
                            <div>End Date And Time</div>
                            <div><?php echo($event["end_date"]);?></div>
                        </div>
                        <div class="details_item">
                            <div>Location</div>
                            <div><?php echo($event["location"]);?></div>
                        </div>
                        <div id="details_item_description" class="details_item">
                            <div>Description</div>
                            <div><?php echo($event["event_description"]);?></div>
                        </div>
                        <div class="details_item">
                        <?php if($event["average_rating"]):?>
                            <div>Rating:</div>
                            <div><?php echo($event["average_rating"])?></div>
                        <?php else:?>
                            <div>Rating:</div>
                            <div>0</div>
                        <?php endif;?>
                        </div>
                        <div class="details_options_pane">
                        <?php if(strtotime($event["start_relative_to_central_time"])>time()):?>
                            <a href="?action=events&sub=edit_event&event_id=<?php echo($event["event_id"])?>">Edit</a>
                        <?php endif;?>
                        <?php if(strtotime($event["start_relative_to_central_time"])<time()&&strtotime($event["end_relative_to_central_time"])>time()):?>
                            <a href="?action=tickets&sub=check_in&event_id=<?php echo($event["event_id"])?>">Ticket Check In</a>
                        <?php endif;?>
                        <a href="?action=events&sub=event_details&event_id=<?php echo($event["event_id"])?>">Event Details</a>
                        </div>
                    </div>
                </div>
                <?php endforeach;?>
            <?php else:?>
                <?php if(isset($_GET["sort"])):?>

                    <?php switch($_GET["sort"]):
                        case "past":?>
                            <div>You do not have past events.</div>
                        <?php break;?>
                        <?php case "current":?>
                            <div>You do not have events that are happening right now,</div>
                        <?php break;?>
                        <?php case "future":?>
                            <div>You do not have events set to start in the future,</div>
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
    </div>
    <div class="tablet">
        <h1>Manage Your Events</h1>
        <div class="event-sort">
            <a href="?action=events&sort=past">Past Events</a>
            <a href="?action=events&sort=current">Current Events</a>
            <a href="?action=events&sort=future">Future Events</a>
        </div>
        <div>
            <?php if(!empty($events)):?>
                <?php foreach ($events as $event):?>
                <div class="event">
                    <div class="event_picture">
                        <?php if($event["picture"]):?>
                            <img src="<?php echo(ConvertToImgString($event["picture"]))?>">
                        <?php else:?>
                            No Picture Found.
                        <?php endif;?>
                    </div>
                    <div class="event_details">
                        <div class="details_item">
                            <div>Event</div>
                            <div> <?php echo($event["name"]);?></div>
                        </div>
                        <div class="details_item">
                            <div>Charging $ </div>
                            <div><?php echo($event["charge"]);?></div>
                        </div>
                        <div class="details_item">
                            <div>Start Date And Time</div>
                            <div><?php echo($event["start_date"]);?></div>
                        </div>
                        <div class="details_item">
                            <div>End Date And Time</div>
                            <div><?php echo($event["end_date"]);?></div>
                        </div>
                        <div class="details_item">
                            <div>Location</div>
                            <div><?php echo($event["location"]);?></div>
                        </div>
                        <div id="details_item_description" class="details_item">
                            <div>Description</div>
                            <div><?php echo($event["event_description"]);?></div>
                        </div>
                        <div class="details_item">
                        <?php if($event["average_rating"]):?>
                            <div>Rating:</div>
                            <div><?php echo($event["average_rating"])?></div>
                        <?php else:?>
                            <div>Rating:</div>
                            <div>0</div>
                        <?php endif;?>
                        </div>
                        <div class="details_options_pane">
                        <?php if(strtotime($event["start_relative_to_central_time"])>time()):?>
                            <a href="?action=events&sub=edit_event&event_id=<?php echo($event["event_id"])?>">Edit</a>
                        <?php endif;?>
                        <?php if(strtotime($event["start_relative_to_central_time"])<time()&&strtotime($event["end_relative_to_central_time"])>time()):?>
                            <a href="?action=tickets&sub=check_in&event_id=<?php echo($event["event_id"])?>">Ticket Check In</a>
                        <?php endif;?>
                        <a href="?action=events&sub=event_details&event_id=<?php echo($event["event_id"])?>">Event Details</a>
                        </div>
                    </div>
                </div>
                <?php endforeach;?>
            <?php else:?>
                <?php if(isset($_GET["sort"])):?>

                    <?php switch($_GET["sort"]):
                        case "past":?>
                            <div>You do not have past events.</div>
                        <?php break;?>
                        <?php case "current":?>
                            <div>You do not have events that are happening right now,</div>
                        <?php break;?>
                        <?php case "future":?>
                            <div>You do not have events set to start in the future,</div>
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
    </div>
    <div class="desktop">
    <h1>Manage Your Events</h1>
    <div class="event_sort">
        <a class="link" href="?action=events&sort=past">Past Events</a>
        <a class="link" href="?action=events&sort=current">Current Events</a>
        <a class="link" href="?action=events&sort=future">Future Events</a>
    </div>
    <div>
        <?php if(!empty($events)):?>
            <?php foreach ($events as $event):?>
            <div class="event">
                <h1><?php echo($event["name"])?></h1>
                <div class="event_picture_and_details">
                    <div class="event_picture">
                        <?php if($event["picture"]):?>
                            <img src="<?php echo(ConvertToImgString($event["picture"]))?>">
                        <?php else:?>
                            No Picture Found.
                        <?php endif;?>
                    </div>
                    <div class="event_details">
                        <div class="event_details_split_double">
                            <div class="details_item">
                                <div>Charge</div>
                                <div><?php echo($event["charge"]);?></div>    
                            </div>
                            <div class="details_item">
                                <div>Start Date Time</div>
                                <div><?php echo($event["start_date"]);?></div>    
                            </div>
                        </div>
                        <div class="event_details_split_double">
                            <div class="details_item">
                                <div>End Date Time</div>
                                <div><?php echo($event["end_date"]);?></div>    
                            </div>
                            <div class="details_item">
                                <div>Time Zone</div>
                                <div><?php echo($event["time_zone"]);?></div>    
                            </div>
                        </div>
                        <div class="event_details_split_double">
                            <div class="details_item">
                                <div>Location</div>
                                <div><?php echo($event["location"]);?></div> 
                            </div>
                        </div>
                    </div>
                </div>
                <div class="details_options_pane">
                    <?php if(strtotime($event["start_relative_to_central_time"]) > time()):?>
                        <a class="link" href="?action=events&sub=edit_event&event_id=<?php echo($event["event_id"])?>">Edit</a>
                    <?php endif;?>
                    <?php if(strtotime($event["start_relative_to_central_time"]) < time() && strtotime($event["end_relative_to_central_time"]) > time()):?>
                        <a class="link" href="?action=tickets&sub=check_in&event_id=<?php echo($event["event_id"])?>">Ticket Check In</a>
                    <?php endif;?>
                    <a class="link" href="?action=events&sub=event_details&event_id=<?php echo($event["event_id"])?>">Event Details</a>
                </div>
            </div>
            <?php endforeach;?>
        <?php else:?>
            <?php if(isset($_GET["sort"])):?>
                <?php switch($_GET["sort"]):
                    case "past":?>
                        <div>You do not have past events.</div>
                    <?php break;?>
                    <?php case "current":?>
                        <div>You do not have events that are happening right now.</div>
                    <?php break;?>
                    <?php case "future":?>
                        <div>You do not have events set to start in the future.</div>
                    <?php break;?>
                    <?php default:?>
                        <div>No events to display. Start an event today.</div>
                    <?php break;?>
                <?php endswitch;?>
            <?php else:?>
                <div>No events to display. Start an event today.</div>
            <?php endif;?>
        <?php endif;?>
    </div>
</div>

</div>
