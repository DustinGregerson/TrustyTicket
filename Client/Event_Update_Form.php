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
        $event_id=$record["event_id"];
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
			        <input type="number" min="<?php echo($max_seats)?>" step="any" max="100" name=max_seats value="<?php echo($max_seats);?>">
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
        var apiCall=$("#api_function_call").val();
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
            console.log(data);
            var errorValue=false;
            window.location.href="http://localhost/project/trustyticket";
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
                console.error('Error:', textStatus, errorThrown);
                console.log(data);
        });
    });
</script>

<!--
	<form id="target" method=POST action="ServiceProvider/API.php" enctype="multipart/form-data">
		<input type="hidden" name="api_function_call" value=update_event>

			<input type="hidden" hidden name=event_id value="<?php echo($event_id);?>">
            
			<label for="name">Name:</label>
			<input type="text" name=name value="<?php echo($name)?>">
         
			<label for="event_description">Description:</label>
			<textarea name=event_description><?php echo($event_description)?></textarea>

          
			<label for="max_seats">Number of Attendants:</label>
			<input type="number" min="<?php echo($max_seats)?>" step="any" max="100" name=max_seats value="<?php echo($max_seats);?>">


            <?php if(($times_changed<3) || (!$ticketsBought)):?>
                <label for="event_start">Start Date:</label>
                <input type="datetime-local" name=event_start value="<?php echo($event_start)?>">

                <label for="event_end">End Date:</label>
                <input type="datetime-local" name=event_end value="<?php echo($event_end)?>">
          
            <?php else:?>
                <label for="event_start">Start Date:</label>
                <input type="datetime-local" readonly name=event_start value="<?php echo($event_start)?>">

                <label for="event_end">End Date:</label>
                <input type="datetime-local" readonly name=event_end value="<?php echo($event_end)?>">
            <?php endif;?>

			<label for="charge">Charge Per Ticket:</label>
			<input type="number" max="9999" min="0.01"step="0.01"name="charge"value="<?php echo($charge)?>">
            
			<label for="times_changed"># of Updates To This event</label>
			<input type="number" min=1 step="any" max="3" readonly name=times_changed value="<?php echo($times_changed);?>">
           
			<div>Make event visiable on site:
            <?php if($show_event):?>
                   
                    <?php if(!$ticketsBought):?>
                        <label for="show_event"> yes </label>
                        <input id="show_event" checked type="radio" name="show_event" value="1">

                        <label for="show_event"> no </label>
                        <input id="show_event"  type="radio" name="show_event" value="0">
                  </div>
               
                    <?php else:?>
                        <label for="show_event"> yes </label>
                        <input id="show_event" checked readonly type="radio" name="show_event" value="1">

                        <label for="show_event"> no </label>
                        <input id="show_event" disabled type="radio" name="show_event" value="0">
                  </div>
                    <?php endif;?>

            <?php else:?>
                    
                    <?php if(!$ticketsBought):?>

                    <label for="show_event"> yes </label>
                    <input id="show_event" type="radio" name="show_event" value="1">

                    <label for="show_event"> no </label>
                    <input id="show_event" checked type="radio" name="show_event" value="0">

                </div>
                    
                    <?php else:?>
                    <label for="show_event"> yes </label>
                    <input id="show_event" disabled type="radio" name="show_event" value="1">

                    <label for="show_event"> no </label>
                    <input id="show_event" checked type="radio" name="show_event" value="0">
                </div>
                    <?php endif;?>
            <?php endif;?>
            
			<div>Private Event:
          
                <?php if($private_event):?>
                   
                    <?php if(!$ticketsBought):?>
                        <label for="private_event"> yes </label>
                        <input id="private_event" checked type="radio" name="private_event" value="1">

                        <label for="private_event"> no </label>
                        <input id="private_event"  type="radio" name="private_event" value="0">
                 
                    <?php else:?>
                        <label for="private_event"> yes </label>
                        <input id="private_event" checked type="radio" name="private_event" value="1">

                        <label for="private_event"> no </label>
                        <input id="private_event" disabled type="radio" name="private_event" value="0">
                    <?php endif;?>
                </div>

                <?php else:?>
                     
                    <?php if(!$ticketsBought):?>
                        <label for="private_event"> yes </label>
                        <input id="private_event" type="radio" name="private_event" value="1">
                        
                        <label for="private_event"> no </label>
                        <input id="private_event" checked type="radio" name="private_event" value="0">
                  
                    <?php else:?>

                        <label for="private_event"> yes </label>
			            <input id="private_event" disabled type="radio" name="private_event" value="1">

				        <label for="private_event"> no </label>
				        <input id="private_event" checked type="radio" name="private_event" value="0">

                    <?php endif;?>
                </div>
            <?php endif;?>
    <div>
        <img src="<?php echo(ConvertToImgString($picture))?>">
    </div>
    <input type="file" accept="image/*" name="image">
	<button type="submit">submit</button>
</form>


                    