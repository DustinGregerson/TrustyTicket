<?php
include_once("ServiceProvider/Class_Lib/Rating_Manager.php");
$rating_manager=new Rating_Manager();
$rating=$rating_manager->getHostRating($_SESSION["user_id"]);

?>
<div id="Hub">
    <div class="text_content">
        Welcome back to TrustyTicket
    </div>
    <div class="host-rating">
                <h3>Your Host Rating:
                    <?php if($rating_manager->getHostRating($_SESSION["user_id"])):?>
                    <span id="host-rating"><?php echo($rating["host_rating"])?></span>
                    <?php else:?>
                    <span id="host-rating">You do not have a rating when you host an event people can rate your events
                                            and you can start accumulating a rating.
                    </span>
                    <?php endif;?>
                </h3>
    </div>
</div>
