<?php

include_once("ServiceProvider/Class_Lib/Ticket_Manager.php");
$ticket_Manager=new Ticket_Manager();
$tickets=$ticket_Manager->getAllTicketInformationForEvent($_GET["event_id"]);
?>
<div id="Ticket_Check_In">
    <div>
        <form id="target">
            <input type="hidden" name="event_id" value="<?php echo($_GET["event_id"])?>">
            <input type="hidden" name="api_function_call" value="use_ticket">
            <div class="label_input">
                <label>Enter Ticket Code For Attendant</label>
                <input type="text" name="code">
            </div>
            <div class="button">
                <button type="submit">Check In</button>
            </div>
        </form>
    </div>
    <table>
        <thead>
            <tr>
                <th>
                    Ticket Code
                </th>
                <th>
                    Used
                </th>
            </tr>
        </thead>
        <tbody>
    <?php foreach($tickets as $ticket):?>
                <tr>
                    <td><?php echo($ticket["code"])?></td>
                    <td>
                        <?php if(($ticket["used"])):?>
                            Yes
                        <?php else:?>
                            NO
                        <?php endif;?>
                    </td>
                </tr>
    <?php endforeach;?>
        </tbody>
    </table>
</div>

<script>
    $('#target').submit(function(event) {
        event.preventDefault();
        var type='POST';
        var formData=$(this).serialize();
        var error=$("#error");
        var data={};
    $.ajax({
                type: type,
                url: 'http://localhost/project/trustyticket/serviceProvider/API.php',
                data: formData , 
                dataType: 'json',
                encode: true
            })
            .done(function(data) {
                console.log(data);
                if(data==true){
                    alert("Approved");
                    window.location="";
                }
                else{
                    alert("Not Approved");
                    window.location="";
                }
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                    console.error('Error:', textStatus, errorThrown);
                    console.log(data);
            });
        });
</script>
