<?php

        include_once("ServiceProvider/Class_Lib/Events_Manager.php");
        include_once("ServiceProvider/Class_Lib/Ticket_Manager.php");
        include_once("ServiceProvider/Class_Lib/Picture_Formatter.php");
        $event_manager=new Events_Manager();
        $ticket_Manager=new Ticket_Manager();
        $ticketsBought=$ticket_Manager->getTicketsForEvent($_GET["event_id"])["ticket_sold"];
        $record=$event_manager->getRecord();
        $categories=$event_manager->getCategories();

        $event_id=$record["event_id"];
        $name=$record["name"];
        $event_description=$record["event_description"];
        $max_seats=$record["max_seats"];
        $event_start=$record["start_date"];
        $event_end=$record["end_date"];
        $event_relative_to_central_time=$record["start_relative_to_central_time"];
        $charge=$record["charge"];
        $time_zone=$record["time_zone"];
        $times_changed=$record["times_changed"];
        $show_event=$record["show_event"];
        $private_event=$record["private_event"];
        $event_code=$record["event_code"];
        $picture_id=$record["picture_id"];
        $picture=$record["picture"];
        

        /*
            This is where were going to check
            A.The user altering this event is the host
            B.The event has not ended

            A OR B
            Will result in a page redirect back to the users hub or the landing page.

            There will not be a error message because the user should not have gotten to this page through normal means
            if A OR B are tripped.
        */

        //There is no reason this should happen unless someone is doing something that they shouldn't be doing
        //I will implement a banning system for this in the future.
        if($_SESSION["user_id"]!=$record["user_id"]){
            session_destroy();
            header("Location:index.php");
        }
        //A user may be able to do this on accident they will be returned to the hub.
        if(strtotime($event_relative_to_central_time)<time()){
            //header("Location:index.php");
        }
?>
        <div id="Create_Update_Event">
            <h1>Update Event</h1>
            <form id="target" method=POST action="ServiceProvider/API.php" enctype="multipart/form-data">
                <input type="hidden" name="api_function_call" value=update_event>
                <input type="hidden" hidden name=event_id value="<?php echo($event_id);?>">
                <!--Name-->
                <div class="label_input">
                    <label for="name">Event Name *</label>
                    <input type="text" name=name value="<?php echo($name)?>">
                </div>



                <?php if(($times_changed<3) || (!$ticketsBought)):?>
                    <div class="label_error_input">
                        <label for="event_start">Start of event *</label>
                        <span id="start_date_error"></span>
                        <input type="datetime-local" name=start_date value="<?php echo($event_start)?>">
                    </div>
                    <div class="label_error_input">
                        <label for="end_date">End of Event *</label>
                        <span id="end_date_error"></span>
                        <input type="datetime-local" name=end_date value="<?php echo($event_end)?>">
                    </div>
                <?php else:?>
                    <div class="label_error_input">
                        <label for="start_date">Start of event *</label>
                        <span id="start_date_error"></span>
                        <input type="datetime-local" readonly name=start_date value="<?php echo($event_start)?>">
                    </div>
                    <div class="label_error_input">
                        <label for="event_end">End of Event *</label>
                        <span id="end_date_error"></span>
                        <input type="datetime-local" readonly name=end_date value="<?php echo($event_end)?>">
                    </div>
                <?php endif;?>

                <?php if(($times_changed<3)||(!$ticketsBought)):?>
                <div class="label_select">
                    <label for="time_Zone">Time Zone*</label>
                    <select name="time_zone">
                        <?php if($time_zone=="Eastern"):?>
                            <option selected value="Eastern">Eastern</option>
                            <option value="Central">Central</option>
                            <option value="Mountain">Mountain</option>
                            <option value="Pacific">Pacific</option>
                        <?php elseif($time_zone=="Central"):?>
                            <option value="Eastern">Eastern</option>
                            <option selected value="Central">Central</option>
                            <option value="Mountain">Mountain</option>
                            <option value="Pacific">Pacific</option>
                        <?php elseif($time_zone=="Mountain"):?>
                            <option value="Eastern">Eastern</option>
                            <option value="Central">Central</option>
                            <option selected value="Mountain">Mountain</option>
                            <option value="Pacific">Pacific</option>
                        <?php elseif($time_zone=="Pacific"):?>
                            <option value="Eastern">Eastern</option>
                            <option value="Central">Central</option>
                            <option value="Mountain">Mountain</option>
                            <option selected value="Pacific">Pacific</option>
                        <?php endif;?>
                    </select required>
                </div>
                <?php else:?>
                    <div class="label_input">
                    <label for="time_Zone">Time Zone*</label>
                    <input readonly name="time_zone" value="<?php echo($time_zone)?>">
                    </div>
                <?php endif;?>

                <div class="label_error_input">
                    <label for="max_seats">Number of Seats*</label>
                    <span id="seat_error"></span>
			        <input type="number" min="<?php echo($ticketsBought)?>" step="any" max="100" name=max_seats value="<?php echo($max_seats);?>">
                </div>

                <!--Charge-->
                <div class="label_input">
                    <label for="charge">Charge *</label>
                    <input type="number" max="9999" min="0.01"step="0.01"name="charge"value="<?php echo($charge)?>">
                </div>

                <div class="label_select">
                    <label>Category</label>
                    <select name="event_category_id">
                        <?php foreach($categories as $category):?>
                            <option value="<?php echo $category["event_category_id"]?>"><?php echo $category["category"]?></option>
                        <?php endforeach;?>
                    </select>
                </div>

                <!--Description-->
                <div class="label_text_area">
                    <label for="event_description">Event Description *</label>
                    <textarea name=event_description><?php echo($event_description)?></textarea>
                </div>

                <!--Show Event-->
                <div class="Yes_No_Radio">
                    <span>Visible On Site:</span>
                    <?php if($show_event):?>
                        <!--Radio Buttons Enabled--->
                        <?php if(!$ticketsBought):?>
                            <div>
                                <label for="show_event"> yes </label>
                                <input id="show_event" checked type="radio" name="show_event" value="1">

                                <label for="show_event"> no </label>
                                <input id="show_event"  type="radio" name="show_event" value="0">
                            </div>
                </div>
                  <!--Radio Buttons Disabled--->
                    <?php else:?>
                        <div>
                            <label for="show_event"> yes </label>
                            <input id="show_event" checked readonly type="radio" name="show_event" value="1">

                            <label for="show_event"> no </label>
                            <input id="show_event" disabled type="radio" name="show_event" value="0">
                        </div>
                  </div>
                    <?php endif;?>

                    <?php else:?>
                            <!--Radio Buttons Enabled--->
                            <?php if(!$ticketsBought):?>
                            <div>
                                <label for="show_event"> yes </label>
                                <input id="show_event" type="radio" name="show_event" value="1">

                                <label for="show_event"> no </label>
                                <input id="show_event" checked type="radio" name="show_event" value="0">
                            </div>

                        </div>
                            <!--Radio Buttons Disabled--->
                            <?php else:?>
                            <div>
                                <label for="show_event"> yes </label>
                                <input id="show_event" type="radio" name="show_event" value="1">

                                <label for="show_event"> no </label>
                                <input id="show_event" checked type="radio" name="show_event" value="0">
                            </div>
                        </div>
                            <?php endif;?>
                    <?php endif;?>
                


                <div class="Yes_No_Radio"><span>Private Event:</span>
                    
                <?php if($private_event):?>
                    <!--Radio Buttons Enabled--->
                    <?php if(!$ticketsBought):?>
                        <div>
                            <label for="private_event"> yes </label>
                            <input id="private_event" checked type="radio" name="private_event" value="1">

                            <label for="private_event"> no </label>
                            <input id="private_event"  type="radio" name="private_event" value="0">
                        </div>
                    <!--Radio Buttons Disabled--->
                    <?php else:?>
                        <div>
                            <label for="private_event"> yes </label>
                            <input id="private_event" checked type="radio" name="private_event" value="1">

                            <label for="private_event"> no </label>
                            <input id="private_event" disabled type="radio" name="private_event" value="0">
                        </div>
                    <?php endif;?>
                </div>
                <!--Public Event--->
                <?php else:?>
                        <!--Radio Buttons Enabled--->
                    <?php if(!$ticketsBought):?>
                        <div>
                            <label for="private_event"> yes </label>
                            <input id="private_event" type="radio" name="private_event" value="1">
                            
                            <label for="private_event"> no </label>
                            <input id="private_event" checked type="radio" name="private_event" value="0">
                        </div>
                        <!--Radio Buttons Disabled--->
                    <?php else:?>
                        <div>
                            <label for="private_event"> yes </label>
                            <input id="private_event" disabled type="radio" name="private_event" value="1">

                            <label for="private_event"> no </label>
                            <input id="private_event" checked type="radio" name="private_event" value="0">
                        </div>
                    <?php endif;?>
                </div>
                <?php endif;?> 

                <!--Pictures for event-->
                <div class="label_img_input">
                    <label for="picture">change picture: </label>    
                    <input type="file" accept="image/*" name="image"> 
                    <div>
                        <img src="<?php echo(ConvertToImgString($picture))?>">
                    </div>
                </div>

                <div class="button">
                    <button type="submit">Save</button>
                </div>
            </form>  
        </div>
<script>
    $('#target').submit(function(event) {
        event.preventDefault();
        var type='POST';
        var formData=new FormData(this);
        var error=$("#error");
        var data={};
        
        $.ajax({
            type: type,
            url: 'http://localhost/project/trustyticket/serviceProvider/API.php',
            data: formData ,
            processData: false,
            contentType: false, 
            dataType: 'json',
            encode: true
        })
        .done(function(data) {
            var message="";

            if(data===true){
                window.location.href="http://localhost/project/trustyticket?action=events&sub=event_details&event_id=<?php echo($_GET["event_id"])?>";
            }
            /*
                In typical use cases, these errors should only come up once because the server will return the html document with
                with input fields as display only, or the radio buttons will be disabled if the event values can not be changed.
                However, if a ticket is bought in the middle of a user update, then these errors can occur.

                Or someone is doing something that they shouldn't be doing that can land them in federal prison....
            */
            switch(Number(data)){
                case 45000:message="'You can not change the time zone because tickets for the event have been purchased.";
                break;
                case 45001:message="The event dates can not be changed because the event has started.";
                break;
                case 45002:message="You can not update the event dates because you have already changed it three times.";
                break;
                case 45003:message="The event start dates or event end dates can not be less than there original set dates because tickets have already been purchased.";
                    $("#start_date_error").html(message);
                break;
                case 45004:message="The length of the event must be less than 14 days in length";
                    $("#start_date_error").html(message);
                break;
                case 45005:message="You can not stop showing the event on the site because tickets have been sold for it.";
                break;
                case 45006:message="You can not update the number of seats to number less than the number of tickets sold or above 100 seats.";
                    $("#seat_error").html(message);
                break;
                case 45007:message="The events privacy can not be changed tickets have already been purchased";
                break;
                case 45008:message="Your start date and time can not before the current date and time. Did you enter the correct timezone?";
                    $("#start_date_error").html(message);
                case 45009:message="Your event start date and time can not be before your event end date and time."
                    $("#start_date_error").html(message);
                default:console.log(data);//window.location.href="http://localhost/project/trustyticket";
            }
            console.log(message);
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
                console.error('Error:', textStatus, errorThrown);
                console.log(data);
        });
    });
</script>


                    