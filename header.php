<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TrustyTicket</title>
    <link rel="stylesheet" href="reset.css">
    <link rel="stylesheet" href="layout.css">
    <link rel="stylesheet" href="Font_Pad_Marg.css">
    <link rel="stylesheet" href="color.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="test.js"></script>
</head>

<?php if(!isset($_SESSION["user_id"])):?>
    <body id="Login_Or_Register">
<?php else:?>
    <body>
<?php endif;?>
<!--After Login-->
<?php if(isset($_SESSION["user_id"])):?>
    <div class="mobile">
        <nav>
            <h1>Navigation</h1>
            <ul>
                <li><a href="?action=events&sub=add_event" class="create-event">+ Create Events</a></li>
                <li><a href="?action=hub">Dashboard</a></li>
                <li><a href="?action=notifications">Notifications</a></li>
                <li><a href="?action=events">Manage Events</a></li>
                <li><a href="?action=events&sub=find_events">Find Events</a></li>
                <li><a href="?action=tickets">Tickets</a></li>
                <li><a href="?action=dispute">Disputes</a></li>
                <li><a href="?action=financial">Financial</a></li>
                <li><a href="?action=ban_user">Ban A User</a></li>
                <li><a href="?action=list_banned_users">Banned Users List</a></li>
                <li><a href="?action=logout">Log Out</a></li>
            </ul>
        </nav>
        <div class="user">
            <h3><?php echo($_SESSION["username"])?></h3>
        </div>  
    </div>

    <div id="Nav_Ribbon" class="tablet_and_desktop">
            <nav>
            <div class="user">
                <h3><?php echo($_SESSION["username"])?></h3>
            </div>
            <ul>
                <li><a class="link" href="?action=events&sub=add_event" class="create-event">+ Create Events</a></li>
                <li><a class="link" href="?action=hub">Dashboard</a></li>
                <li><a class="link" href="?action=notifications">Notifications</a></li>
                <li><a class="link" href="?action=events">Manage Events</a></li>
                <li><a class="link" href="?action=events&sub=find_events">Find Events</a></li>
                <li><a class="link" href="?action=tickets">Tickets</a></li>
                <li><a class="link" href="?action=dispute">Disputes</a></li>
                <li><a class="link" href="?action=financial">Financial</a></li>
                <li><a class="link" href="?action=ban_user">Ban A User</a></li>
                <li><a class="link" href="?action=list_banned_users">Banned Users List</a></li>
                <li><a class="link" href="?action=logout">Log Out</a></li>
            </ul>
        </nav>
    </div>  
<?php else:?>

    <header>
            <div id="logo">TrustyTicket</div>
    </header>

<?php endif;?>


