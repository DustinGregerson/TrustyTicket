
<?php
    include_once("ServiceProvider/Class_Lib/Events_Manager.php");
    $event_manager=new Events_Manager();
    $categories=$event_manager->getCategories();
?>
<div id="Create_Update_Event">
    <h1>Create a new event</h1>
    <form id="target" enctype="multipart/form-data">
        <input type="hidden" name="api_function_call" value=insert_event>

        <!--Name-->
        <div class="label_input">
            <label for="name">Event Name *</label>
            <input type="text" name="name" required>
        </div>

        <!--Start date-->
        <div class="label_error_input">
            <label for="start_date">Start of event *</label> 
            <span id="Error_Dates"></span>
            <input type="datetime-local" name="start_date" required>
        </div>

        <!--End date-->
        <div class="label_input">
            <label for="end_date">End of Event *</label> 
            <input type="datetime-local" name="end_date" required>
        </div>

        <div class="label_select">
            <label for="time_Zone">Time Zone*</label>
            <select name="time_zone">
                <option value="Eastern">Eastern</option>
                <option value="Central">Central</option>
                <option value="Mountain">Mountain</option>
                <option value="Pacific">Pacific</option>
            </select required>
        </div>

        <!--Max Seats-->
        <div class="label_input">
            <label for="max_seats">Max Seat *</label>
            <input type="number" min=1 step="any" max="100" name="max_seats" value="1" required>
        </div>

        <!--Charge-->
        <div class="label_input">
            <label for="charge">Charge $*</label>
            <input type="number" max="99.99" min="1.00"step="0.01"name="charge" value="1" required>
        </div>

        <div class="label_input">
            <label for="location">Location *</label>
            <input type="text" name="location" required>
        </div>

        <div class="label_select">
            <label>Category</label>
            <select name="event_category_id">
                <?php foreach($categories as $category):?>
                    <option value="<?php echo $category["event_category_id"]?>"><?php echo $category["category"]?></option>
                <?php endforeach;?>
            </select required>
        </div>
        <!--Description-->
        <div class="label_text_area">
            <label for="event_description">Event Description *</label>
            <textarea name="event_description"></textarea>
        </div>

        <!--Show Event-->
        <div class="Yes_No_Radio">
            <span>Visible On Site:</span>
            <div>
                <label for="show_event">Yes</label>
                <input id="show_event" type="radio" name="show_event" value="1">
                <label for="show_event">No</label>
                <input id="show_event" checked type="radio" name="show_event" value="0">
            </div>
        </div>

        <div class="Yes_No_Radio">
            <span>Private Event:</span>
            <div>
                <label for="private_event">Yes</label>          
                <input id="private_event" type="radio" name="private_event" value="1">
                <label for="private_event">No</label>
                <input id="private_event" checked type="radio" name="private_event" value="0">
            </div>     
        </div>
        <!--Pictures for event-->
        <div class="label_input">
            <label for="picture">Upload a picture: </label>    
            <input type="file" accept="image/*" name="image">
        </div>
        <div class="button">
        <button type="submit">Save</button>
        </div>
    </form> 
</div> 
<script>
    $('#target').submit(function(event) {
        event.preventDefault();
        var apiCall=$("#api_function_call").val();
        var type='POST';
        var formData=new FormData(this);
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
            if(data["date_fail"]){
                var error=$("#Error_Dates");
                error.html(data["date_fail"]);
            }
            else{
                window.location.href="http://localhost/project/trustyticket?action=events";
            }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
                console.error('Error:', textStatus, errorThrown);
                console.log(data);
                window.location.href="http://localhost/project/trustyticket";
        });
    });
</script>
