<?php
include_once("ServiceProvider/Class_Lib/Account_Manager.php");
$accountManager=new Account_Manager();
$notifications=$accountManager->getNotifications();
?>
<div id="Notifications">
<?php if(empty($notifications)):?>
    <div>You do not have notifications</div>
<?php else:?>
    <?php foreach($notifications as $notification):?>
        <div class="notification"><?php echo($notification["message"])?></div>
    <?php endforeach;?>
<?php endif;?>
</div>