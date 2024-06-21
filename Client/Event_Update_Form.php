<?php

        include_once("ServiceProvider/Class_Lib/Events_Manager.php");
        include_once("ServiceProvider/Class_Lib/Picture_Formatter.php");
        $event_manager=new Events_Manager();
        $ticketsBought=$event_manager->getTicketCount();
        $record=$event_manager->getRecord();


        $event_id=$record["event_id"];
        $name=$record["name"];
        $event_description=$record["event_description"];
        $max_seats=$record["max_seats"];
        $event_start=$record["event_start"];
        $event_end=$record["event_end"];
        $charge=$record["charge"];
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
        if(strtotime($record["event_start"])<time()){
            header("Location:index.php");
        }
        
    $ticketsBought=$ticketsBought["count(*)"];
?>
        <main class="create_form">
            <section class="create_event_form">
                <h1>Update Event</h1>
            <form id="target" method=POST action="" enctype="multipart/form-data">
                <input type="hidden" name="api_function_call" value=update_event>
                <input type="hidden" hidden name=event_id value="<?php echo($event_id);?>">
                <!--Name-->
                <div class="form_name">
                    <label for="name">Event Name *</label>
                    <input type="text" name=name value="<?php echo($name)?>">
                </div>



                <?php if(($times_changed<3) || (!$ticketsBought)):?>
                    <div class="form-start">
                        <label for="event_start">Start of event *</label>
                        <input type="datetime-local" name=event_start value="<?php echo($event_start)?>">
                    </div>
                    <div class="form-end">
                        <label for="event_end">End of Event *</label>
                        <input type="datetime-local" name=event_end value="<?php echo($event_end)?>">
                    </div>
                <?php else:?>
                    <div class="form-start">
                        <label for="event_start">Start of event *</label>
                        <input type="datetime-local" readonly name=event_start value="<?php echo($event_start)?>">
                    </div>
                    <div class="form-end">
                        <label for="event_end">End of Event *</label>
                        <input type="datetime-local" readonly name=event_end value="<?php echo($event_end)?>">
                    </div>
                <?php endif;?>


                <div class="form_seat">
                    <label for="max_seats">Number of Seats*</label>
			        <input type="number" min="<?php echo($ticketsBought)?>" step="any" max="100" name=max_seats value="<?php echo($max_seats);?>">
                </div>

                <!--Charge-->
                <div class="form_charge">
                    <label for="charge">Charge *</label>
                    <input type="number" max="9999" min="0.01"step="0.01"name="charge"value="<?php echo($charge)?>">
                </div>

                <!--Description-->
                <div class="form_description">
                    <label for="event_description">Event Description *</label>
                    <textarea name=event_description><?php echo($event_description)?></textarea>
                </div>

                <!--Show Event-->
                <div class="form_show_event"><span>Visible On Site:</span>
                <?php if($show_event):?>
                    <!--Radio Buttons Enabled--->
                    <?php if(!$ticketsBought):?>
                        <label for="show_event"> yes </label>
                        <input id="show_event" checked type="radio" name="show_event" value="1">

                        <label for="show_event"> no </label>
                        <input id="show_event"  type="radio" name="show_event" value="0">
                  </div>
                  <!--Radio Buttons Disabled--->
                    <?php else:?>
                        <label for="show_event"> yes </label>
                        <input id="show_event" checked readonly type="radio" name="show_event" value="1">

                        <label for="show_event"> no </label>
                        <input id="show_event" disabled type="radio" name="show_event" value="0">
                  </div>
                    <?php endif;?>

                    <?php else:?>
                            <!--Radio Buttons Enabled--->
                            <?php if(!$ticketsBought):?>

                            <label for="show_event"> yes </label>
                            <input id="show_event" type="radio" name="show_event" value="1">

                            <label for="show_event"> no </label>
                            <input id="show_event" checked type="radio" name="show_event" value="0">

                        </div>
                            <!--Radio Buttons Disabled--->
                            <?php else:?>
                            <label for="show_event"> yes </label>
                            <input id="show_event" type="radio" name="show_event" value="1">

                            <label for="show_event"> no </label>
                            <input id="show_event" checked type="radio" name="show_event" value="0">
                        </div>
                            <?php endif;?>
                    <?php endif;?>
                


                <div class="form_private_event"><span>Private Event:</span>
                    
                <?php if($private_event):?>
                    <!--Radio Buttons Enabled--->
                    <?php if(!$ticketsBought):?>
                        <label for="private_event"> yes </label>
                        <input id="private_event" checked type="radio" name="private_event" value="1">

                        <label for="private_event"> no </label>
                        <input id="private_event"  type="radio" name="private_event" value="0">
                    <!--Radio Buttons Disabled--->
                    <?php else:?>
                        <label for="private_event"> yes </label>
                        <input id="private_event" checked type="radio" name="private_event" value="1">

                        <label for="private_event"> no </label>
                        <input id="private_event" disabled type="radio" name="private_event" value="0">
                    <?php endif;?>
                </div>
                <!--Public Event--->
                <?php else:?>
                        <!--Radio Buttons Enabled--->
                    <?php if(!$ticketsBought):?>
                        <label for="private_event"> yes </label>
                        <input id="private_event" type="radio" name="private_event" value="1">
                        
                        <label for="private_event"> no </label>
                        <input id="private_event" checked type="radio" name="private_event" value="0">
                        <!--Radio Buttons Disabled--->
                    <?php else:?>

                        <label for="private_event"> yes </label>
			            <input id="private_event" disabled type="radio" name="private_event" value="1">

				        <label for="private_event"> no </label>
				        <input id="private_event" checked type="radio" name="private_event" value="0">

                    <?php endif;?>
                </div>
                <?php endif;?> 

                <!--Pictures for event-->
                <div class="form_pictures">
                <img src="<?php echo(ConvertToImgString($picture))?>">
                <label for="picture">change picture: </label>    
                <input type="file" accept="image/*" name="image"> <!--width 482 height 87-->
                </div>

                <button type="submit">Save</button>
            </form>  
            </section>   
        </main>
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
            console.log(data);

            if(data===true){
                window.location.href="http://localhost/project/trustyticket";
            }
            /*
                In typical use cases, these errors should only come up once because the server will return the html document with
                with input fields as display only, or the radio buttons will be disabled if the event values can not be changed.
                However, if a ticket is bought in the middle of a user update, then these errors can occur.

                Or someone is doing something that they shouldn't be doing that can land them in federal prison....
            */
            switch(Number(data)){
                case 45000:message="You can not stop showing the event on the site because tickets have been sold for it";
                break;
                case 45001:message="The event dates can not be changed because the event has started";
                break;
                case 45002:message="You can not update this event dates because you have already changed them three times.";
                break;
                case 45003:message="You can not update the number of seats to a number less than the number of tickets sold or above 100 seats";
                break;
                case 45004:message="event start date or event end date can not be less than there original set dates because tickets have already been purchased";
                break;
                case 45005:message="The length of the event must be between 1-14 days";
                break;
                case 45006:message="The events privacy can not be changed tickets have already been purchased for this event";
                break;
                default:message="An error has occured please try again or call customer Support.";
            }
            console.log(message);
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
                console.error('Error:', textStatus, errorThrown);
                console.log(data);
        });
    });
</script>


                    