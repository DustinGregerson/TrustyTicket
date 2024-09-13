
<?php
          include_once("ServiceProvider/Class_Lib/Events_Manager.php");
          include_once("ServiceProvider/Class_Lib/Picture_Formatter.php");
          include_once("ServiceProvider/Class_Lib/Ticket_Manager.php");
          $event_manager=new Events_Manager();
          $ticket_Manager=new Ticket_Manager();
          $record=$event_manager->getRecord();


          $tickets=$ticket_Manager->getTicketsForEvent($record["event_id"]);
          $totalPayOut=$tickets["total_payout"];

          $user_id=$record["user_id"];
          $event_id=$record["event_id"];
          $ticketsSold=$ticket_Manager->getAvailableTickets($event_id)["tickets_sold"];
          $name=$record["name"];
          $event_description=$record["event_description"];
          $max_seats=$record["max_seats"];
          $event_rating=$record["average_rating"];
          $location=$record["location"];
          $event_start=$record["start_date"];
          $event_end=$record["end_date"];
          $event_relative_to_central_time=$record["start_relative_to_central_time"];
          $event_code=$record["event_code"];
          $time_zone=$record["time_zone"];
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
          if($user_id==$_SESSION["user_id"]){
            include_once("ServiceProvider/Class_Lib/Dispute_Manager.php");
            $dispute_manager=new Dispute_Manager();
            $dispute_figures=$dispute_manager->getDisputeFiguresForEvent($event_id);
            $disputes_filed=$dispute_figures["number_of_disputes"];
            $disputes_total_withholding=$dispute_figures["total_withheld"];
          }
?>

<div id="Event_Details">
    <h1>Event Details</h1>
    <!--Pictures for event-->
    <div class="event">
      <div class="event_picture_and_details">
        <div id="Event_Details_Picture">
          <img src="<?php echo(ConvertToImgString($picture))?>" width="482" height="87" />
        </div>
        <div id="event_details">
          <div class="event_details_split_double">
          <!--Event Name-->
            <div class="details_item">
              <div>Name</div>
              <div><?php echo($name); ?></div>
            </div>

            <div class="details_item">
              <div>Event Code</div>
              <div ><?php echo($event_code)?></div>
            </div>
          </div>

          <!--Charge-->
          <div class="event_details_split_double">
            <div class="details_item">
              <div>Charge</div>
              <div>$<?php echo($charge)?></div>
            </div>

            <div class="details_item">
              <div>Location</div>
              <div><?php echo($location)?></div>
            </div>
          </div>

          <div class="event_details_split_double">
            <!--Start date-->
            <div class="details_item">
              <div>Start Date</div>
              <div><?php echo($event_start)?></div>
            </div>

            <!--End date-->
            <div class="details_item">
              <div>End Date</div>
              <div><?php echo($event_end)?></div>
            </div>
          </div>

          <div class="event_details_split_double">
            <div class="details_item">
              <div>Time Zone</div>
              <div><?php echo($time_zone)?></div>
            </div>

            <!--Max Seats-->
            <div class="details_item">
              <div>Max Seats</div>
              <div><?php echo($max_seats)?></div>
            </div>
          </div>
        </div>
      </div>
      <div class="event_details_single">
            <div class="details_item">
              <div>Open Seats</div>
              <div><?php echo($max_seats-$ticketsSold)?></div>
            </div>
          </div>
          <div class="event_details_single">
            <div class="details_item">
              <div>Description</div>
              <div><?php echo($event_description)?></div>
            </div>
          </div>
    </div>
    <!-- Buy Ticket Button -->
    <?php if($_SESSION["user_id"]!=$user_id && strtotime($event_relative_to_central_time)>time() && $max_seats>$ticketsSold):
      //If the event does not belong to the host AND 
      //the event has not started                AND   
      //the number of tickets sold does not equal the max number of seats
      //display the buy ticket button?>
      <button class="button_default"><a style="text-decoration:none" href="?action=tickets&sub=buy&event_id=<?php echo($event_id)?>">Buy Ticket</a></button>

    <?php endif;?>
    <!--Event Standing desktop and tablet-->
    <div class="tablet_and_desktop">
    <?php if($_SESSION["user_id"]==$user_id):?>
      <h1>Event Standing</h1>
      <table>
        <thead>
          <tr>
            <th>Event Rating</th>
            <th>Tickets Sold</th>
            <th>Charge Per Ticket</th>
            <th>Potential Payout</th>
            <th>Disputes Filed</th>
            <th>Money Withheld From Disputes</th>
            <th>Payout Total</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <?php if($event_rating):?>
            <td><?php echo($event_rating)?></td>
            <?php else:?>
            <td>Unrated</td>
            <?php endif;?>
            <td><?php echo($ticketsSold)?></td>
            <td><?php echo($charge)?></td>
            <td><?php echo($totalPayOut);?></td>
            <td><?php echo($disputes_filed);?></td>
            <td><?php echo($disputes_total_withholding);?></td>
            <td><?php echo($totalPayOut-$disputes_total_withholding);?></td>
          </tr>
        </tbody>
      </table>
    <?php if($disputes_filed):?>
        <a class="link dark" href="?action=dispute&sub=event_disputes&event_id=<?php echo($event_id)?>">View Disputes</a>
    <?php endif;?>
    <?php endif;?>

    </div>
    <!--Event Standing mobile and tablet-->
    <div class="mobile">
    <?php if($_SESSION["user_id"]==$user_id):?>
      <h1>Event Standing</h1>
      <div class="details_item">
          <div>
              Tickets Sold
          </div>
          <div>
              <?php echo($ticketsSold)?>
          </div>
      </div>
      <div class="details_item">
          <div>
              Charge Per Ticket
          </div>
          <div>
              <?php echo($charge)?>
          </div>
      </div>
      <div class="details_item">
          <div>
              Potential Payout
          </div>
          <div>
              <?php echo($totalPayOut);?>
          </div>
      </div>
      <div class="details_item">
          <div>
              Disputes Filed
          </div>
          <div>
              <?php echo($disputes_filed);?>
          </div>
      </div>
      <div class="details_item">
          <div>
              Money Withheld From Disputes
          </div>
          <div>
              <?php echo($disputes_total_withholding);?>
          </div>
      </div>
      <div class="details_item">
          <div>
              Payout Total
          </div>
          <div>
              <?php echo($totalPayOut-$disputes_total_withholding);?>
          </div>
      </div>
    <?php if($disputes_filed):?>
        <a class="link" href="?action=dispute&sub=event_disputes&event_id=<?php echo($event_id)?>">View Disputes</a>
    <?php endif;?>
    <?php endif;?>
    </div>
</div>
