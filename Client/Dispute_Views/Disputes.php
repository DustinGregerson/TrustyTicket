<?php
include_once("ServiceProvider/Class_Lib/Dispute_Manager.php");
$dispute_manager=new Dispute_Manager();
$disputes=$dispute_manager->getAllDisputes();

?>

<div id="Disputes_List">
    <div class="mobile">
        <h1>Your Disputes</h1>
            <?php foreach($disputes as $dispute):?>
                <div class="dispute">
                <div class="details_item">
                    <div>Dispute ID</div>
                    <div><?php echo($dispute["dispute_id"])?></div>
                </div>
                <div class="details_item">
                    <div>Hosts User Name</div>
                    <div><?php echo($dispute["username"])?></div>
                </div>
                <div class="details_item">
                    <div>Event Name</div>
                    <div><?php echo($dispute["name"])?></div>
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
                        <?php if(empty($dispute_manager->isDisputeOngoing($dispute["dispute_id"]))):?>
                            ongoing
                        <?php else:?>
                            settled
                        <?php endif;?>
                    </div>
                </div>
            </div>
            <?php endforeach;?>
    </div>

    <div class="tablet">
        <h1>Your Disputes</h1>
            <?php foreach($disputes as $dispute):?>
                <div class="dispute">
                <div class="details_item">
                    <div>Dispute ID</div>
                    <div><?php echo($dispute["dispute_id"])?></div>
                </div>
                <div class="details_item">
                    <div>Hosts User Name</div>
                    <div><?php echo($dispute["username"])?></div>
                </div>
                <div class="details_item">
                    <div>Event Name</div>
                    <div><?php echo($dispute["name"])?></div>
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
                        <?php if(empty($dispute_manager->isDisputeOngoing($dispute["dispute_id"]))):?>
                            ongoing
                        <?php else:?>
                            settled
                        <?php endif;?>
                    </div>
                </div>
            </div>
            <?php endforeach;?>
    </div>
    <div class="desktop">
        <thead>
            <tr>
                <th>Hosts User Name</th>
                <th>Event Name</th>
                <th>Ticket Purchase Date</th>
                <th>Ticket Code</th>
                <th>Ticket Used At Event</th>
                <th>Dispute Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <?php foreach($disputes as $dispute):?>
        <tr>
            <td><?php echo($dispute["username"])?></td>
            <td><?php echo($dispute["name"])?></td>
            <td><?php echo($dispute["bought_on"])?></td>
            <td><?php echo($dispute["code"])?></td>
            <?php if($dispute["used"]):?>
                <td>yes</td>
            <?php else:?>
                <td>no</td>
            <?php endif;?>
            <td><?php echo($dispute["date_filed"])?></td>
            <?php if($dispute_manager->isDisputeOngoing($dispute["dispute_id"])):?>
                <td>ongoing</td>
            <?php else:?>
                <td>settled</td>
            <?php endif;?>
        </tr>
        <tr>
            <td>Reason: <?php echo($dispute["reason"])?></td>
        </tr>
        <?php endforeach;?>
    </div>
</div>