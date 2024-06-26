<?php
include_once("ServiceProvider/Class_Lib/Dispute_Manager.php");
$dispute_manager=new Dispute_Manager();
$disputes=$dispute_manager->getAllDisputes();

?>


<table>
    <thead>
        <tr>
            <th>Hosts User Name</th>
            <th>Event Name</th>
            <th>Ticket Purchase Date</th>
            <th>Ticket Code</th>
            <th>Ticket Used At Event</th>
            <th>Dispute Date</th>
            <th>Reason Given</th>
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
        <td><?php echo($dispute["reason"])?></td>
        <?php if($dispute_manager->isDisputeOngoing($dispute["dispute_id"])):?>
            <td>ongoing</td>
        <?php else:?>
            <td>settled</td>
        <?php endif;?>
    </tr>
<?php endforeach;?>