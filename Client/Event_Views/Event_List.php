<?php
include_once("ServiceProvider/Class_Lib/Events_Manager.php");
include_once("ServiceProvider/Class_Lib/Picture_Formatter.php");
include_once("ServiceProvider/Class_Lib/Rating_Manager.php");
$event_manager=new Events_Manager();
$rating_manager=new Rating_Manager();

$events=$event_manager->getAllEventsNotCurrentUser();
$categories=$event_manager->getCategories();

$catFound=false;
?>

<div id="Event_List">
    <div id="Event_List_Search_Field">

        <div class="label_select">
            <label>Category</label>
            <select id="category_select">
                <?php foreach($categories as $category):?>
                    <?php if(isset($_GET["category"])&&$_GET["category"]!="None"&&$_GET["category_id"]==$category["event_category_id"]):?>
                        <?php $catFound=true;?>
                        <option selected value="<?php echo($category["event_category_id"])?>"><?php echo($category["category"])?></option>
                    <?php else:?>
                        <option value="<?php echo($category["event_category_id"])?>"><?php echo($category["category"])?></option>
                    <?php endif;?>
                <?php endforeach;?>
                <?php if($catFound):?>
                    <option value="none">None</option>
                <?php else:?>
                    <option selected value="none">None</option>
                <?php endif;?>
            </select>
        </div>

        <div class="label_input">
            <label>User Name</label>
            <?php if(!isset($_GET["username"])):?>
                <input id="username" type="text">
            <?php else:?>
                <input id="username" type="text" value="<?php echo($_GET["username"])?>">
            <?php endif;?>
        </div>

        <div class="label_input">
            <label>Event Code</label>
            <?php if(!isset($_GET["event_code"])):?>
                <input id="event_code" type="text">
            <?php else:?>
                <input id="event_code" type="text" value="<?php echo $_GET["event_code"]?>">
            <?php endif;?>
        </div>
        
        <div class="button">
            <button onclick="sort()">Search</button>
        </div>
        <div class="button">
            <button onclick="clear_search()">Clear Search</button>
        </div>
    </div>

    <div class="mobile">
        <?php if(!empty($events)): ?>
            <?php foreach ($events as $event): ?>
                <div class="event">
                    <div class="event_picture">
                        <?php if($event["picture"]): ?>
                            <img src="<?php echo(ConvertToImgString($event["picture"])); ?>">
                        <?php else: ?>
                            No Picture Found.
                        <?php endif; ?>
                    </div>
                    <div class="event-details">
                        <h2><?php echo($event["name"]); ?></h2>
                        <p>$<?php echo($event["charge"]); ?></p>
                        <p><?php echo($event["start_date"]); ?> - <?php echo($event["end_date"]); ?></p>
                        <p><?php echo($event["location"]); ?></p>
                        <p><?php echo($event["event_description"]); ?></p>
                        
                        <?php if($rating_manager->getHostRating($event["user_id"])["host_rating"]): ?>
                            <p>Host Rating: <?php echo($rating_manager->getHostRating($event["user_id"])["host_rating"]); ?></p>
                        <?php else: ?>
                            <p>Host Rating: unrated</p>
                        <?php endif; ?>
                        
                        <a href="?action=events&sub=event_details&event_id=<?php echo($event["event_id"]); ?>">Event Details</a>

                        <?php if(strtotime($event["start_relative_to_central_time"]) < time() && strtotime($event["end_relative_to_central_time"]) > time()): ?>
                            <a href="?action=tickets&sub=use&event_id=<?php echo($event["event_id"]); ?>">Use Ticket</a>
                        <?php endif; ?>
                    </div>
                </div> 
            <?php endforeach; ?>
        <?php else: ?>
            <?php if(!isset($_GET["category"]) && !isset($_GET["username"]) && !isset($_GET["event_code"])): ?>
                <div class="text_content">There are no events to display at this time. When another user makes a new event and it is public, it will be shown here.</div>
            <?php else: ?>
                <div>There are no events that match your search</div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="tablet">
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
                            <div class="details_item">
                                <div>Charge</div>
                                <div><?php echo($event["charge"]);?></div>    
                            </div>
                            <div class="details_item">
                                <div>Start Date Time</div>
                                <div><?php echo($event["start_date"]);?></div>    
                            </div>
                            <div class="details_item">
                                <div>End Date Time</div>
                                <div><?php echo($event["end_date"]);?></div>    
                            </div>
                            <div class="details_item">
                                <div>Time Zone</div>
                                <div><?php echo($event["time_zone"]);?></div>    
                            </div>
                            <div class="details_item">
                                <div>Location</div>
                                <div><?php echo($event["location"]);?></div> 
                            </div>
                            
                            <div class="details_item">
                                <div>Host Rating</div>
                                <?php if($rating_manager->getHostRating($event["user_id"])["host_rating"]):?>
                                    <div><?php echo($rating_manager->getHostRating($event["user_id"])["host_rating"])?></div> 
                                <?php else:?>
                                    <div>unrated</div>
                                <?php endif;?>
                            </div>
                                <a href="?action=events&sub=event_details&event_id=<?php echo($event["event_id"])?>">Event Details</a>
                        </div>
                    </div>
                    <div>
                        Description
                        <div>
                            <?php echo($event["event_description"])?>
                        </div>
                    </div>
                </div> 
            <?php endforeach;?>
        <?php else:?>
            <?php if(!isset($_GET["category"])&&!isset($_GET["username"])&&!isset($_GET["event_code"])):?>
                <div class="text_content">There are no events to display at this time. When another user makes a new event and it is public, it will be shown here.</div>
            <?php else:?>
                <div>There are no events that match your search</div>
            <?php endif;?>
        <?php endif;?>
    </div>

    <div class="desktop">
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
                                    <div class="details_item">
                                        <div>Host Rating</div>
                                        <?php if($rating_manager->getHostRating($event["user_id"])["host_rating"]):?>
                                            <div><?php echo($rating_manager->getHostRating($event["user_id"])["host_rating"])?></div> 
                                        <?php else:?>
                                            <div>unrated</div>
                                        <?php endif;?>
                                    </div>
                                </div>
                                <a class="link dark" href="?action=events&sub=event_details&event_id=<?php echo($event["event_id"])?>">Event Details</a>
                        </div>
                    </div>
                    <div>
                        Description
                        <div>
                            <?php echo($event["event_description"])?>
                        </div>
                    </div>
                </div> 
            <?php endforeach;?>
        <?php else:?>
            <?php if(!isset($_GET["category"])&&!isset($_GET["username"])&&!isset($_GET["event_code"])):?>
                <div class="text_content">There are no events to display at this time. When another user makes a new event and it is public, it will be shown here.</div>
            <?php else:?>
                <div>There are no events that match your search</div>
            <?php endif;?>
        <?php endif;?>
    </div> 
</div> 

<script>
    function sort(){
        var category=$("#category_select").val();
        var username=$("#username").val();
        var event_code=$("#event_code").val();

        var params="";
        if(category){
            if(category!="none"){
                params+="&category="+category;
            }
        }
        if(username){
            params+="&username="+username;
        }
        if(event_code){
            params+="&event_code="+event_code;
        }

        window.location="?action=events&sub=find_events"+params;
    }
    function clear_search(){
        window.location="?action=events&sub=find_events";
    }
</script>