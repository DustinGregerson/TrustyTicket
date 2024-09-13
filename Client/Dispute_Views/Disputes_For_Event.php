<?php
include_once("ServiceProvider/Class_Lib/Dispute_Manager.php");
$dispute_manager=new Dispute_Manager();
if(!isset($_GET["sort"])){
    $disputes=$dispute_manager->getAllDisputesForEvent($_GET["event_id"]);
}
else{
    $disputes=$dispute_manager->getDisputesForEventSorted($_GET["event_id"]);
}
?>
<div id="Disputes_For_Event">
    <div id="dispute_options">
        <a class="link" href="?action=dispute&sub=event_disputes&event_id=<?php echo $_GET["event_id"]?>&sort=before">Disputes before event started</a>
        <a class="link" href="?action=dispute&sub=event_disputes&event_id=<?php echo $_GET["event_id"]?>&sort=after">Disputes after event started</a>
    </div>
<div class="mobile">
<h1>Disputed Tickets</h1>
 <div class="mobile">
        <?php foreach($disputes as $dispute):?>
        <div class="dispute">
            <div class="details_item">
                        <div>Dispute ID</div>
                        <div><?php echo($dispute["dispute_id"])?></div>
            </div>
            <div class="details_item">
                <div>Ticket Purchase Date</div>
                <div><?php echo($dispute["bought_on"])?></div>
            </div>
            <div class="details_item">
                <div>Ticket Code</div>
                <div><?php echo($dispute["code"])?></div>
            </div>
            <div class="details_item">
                <div>Ticket Used At Event</div>
                <div>
                    <?php if($dispute["used"]):?>
                        yes
                    <?php else:?>
                        no
                    <?php endif;?>
                </div>
            </div>
            <div class="details_item">
                <div>Dispute Date</div>
                <div><?php echo($dispute["date_filed"])?></div>
            </div>
            <div class="details_item">
                <div>Status</div>
                <div>
                    <?php if($dispute_manager->isDisputeOngoing($dispute["dispute_id"])):?>
                        ongoing
                    <?php else:?>
                        settled
                    <?php endif;?>
                </div>
            </div>
        </div>
        <?php endforeach;?>
    </div>
</div>
<div class="tablet_and_desktop">
    <h1>Disputed Tickets For This Event</h1>
    <table>
        <thead>
            <tr>
                <th>Purchase Date</th>
                <th>Code</th>
                <th>Used At Event</th>
                <th>Dispute Date</th>
                <th>Status</th>
            </tr>
        </thead>
    <?php foreach($disputes as $dispute):?>
        <tr>
            <td><?php echo($dispute["bought_on"])?></td>
            <td><?php echo($dispute["code"])?></td>
            <?php if($dispute["used"]):?>
                <td>yes</td>
            <?php else:?>
                <td>no</td>
            <?php endif;?>
            <td><?php echo($dispute["date_filed"])?></td>
            <?php if(!$dispute_manager->isDisputeOngoing($dispute["dispute_id"])):?>
                <td>ongoing</td>
            <?php else:?>
                <td>settled</td>
            <?php endif;?>
        </tr>
        <tr class="tr_double">
            <td>Reason:
            </td>
            <td><?php echo($dispute["reason"])?>
            </td>
        </tr>
    <?php endforeach;?>
</div>
</div>


