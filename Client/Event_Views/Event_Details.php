
<?php
          include_once("ServiceProvider/Class_Lib/Events_Manager.php");
          include_once("ServiceProvider/Class_Lib/Picture_Formatter.php");
          $event_manager=new Events_Manager();
          $ticketsBought=$event_manager->getTicketCount();
          $record=$event_manager->getRecord();
  
          $user_id=$record["user_id"];
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
          if(isset($record["picture"])){
            $picture=$record["picture"];
          }
          else{
            $picture=null;
          }
?>

<main class="content">
  <section class="display_event_form">
    <h1>Event Details</h1>

    <!--Pictures for event-->
    <div class="detail_picture">
      <label for="picture"></label>
      <img src="<?php echo(ConvertToImgString($picture))?>" width="482" height="87" />
    </div>

    <!--Event Name-->
    <div class="detail_name">
      <label for="name">Name</label>
      <?php echo($name); ?>
    </div>

    <!--Charge-->
    <div class="detail_charge">
      <label for="charge">Charge</label>
      <p id="charge">$<?php echo($charge)?></p>
    </div>

    <!--Start date-->
    <div class="detail-start">
      <label for="event_start">Start Date</label>
      <p id="event_start"><?php echo($event_start)?></p>
    </div>

    <!--End date-->
    <div class="detail-end">
      <label for="event_end">End Date</label>
      <p id="event_end"><?php echo($event_end)?></p>
    </div>

    <!--Max Seats-->
    <div class="detail_seat">
      <label for="max_seats">Max Seats</label>
      <p id="max_seats"><?php echo($max_seats)?></p>
    </div>

    <!-- Buy Ticket Button -->
    <?php if($_SESSION["user_id"]!=$user_id && strtotime($event_start>time() && $max_seats<$ticketsBought)):
      //If the event does not belong to the host AND 
      //the event has not started                AND   
      //the number of tickets sold does not equal the max number of seats
      //display the buy ticket button?>
    <div class="buy-ticket">
            <button><a style="text-decoration:none" href="?action=buy_tickets&event_id=<?php echo($event_id)?>">Buy Ticket</a></button>
    </div>
    <?php endif;?>

    <!--Description-->
    <div class="detail_description">
      <label for="event_description">Event Description</label>
      <p id="event_description">
        <?php echo($event_description)?>
      </p>
    </div>
  </section>
</main>
