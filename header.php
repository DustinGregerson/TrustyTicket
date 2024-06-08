<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TrustyTicket</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<?php if($_GET["action"]=="hub"):?>
    <body class="hub">
<?php else:?>
    <body>
<?php endif;?>
<div class="container">
        <nav class="sidebar">
            <div class="sidebar-header">
                <h1>TrustyTicket</h1>
            </div>
            <ul>
                <li><a href="?sub=add_event" class="create-event">+ Create Events</a></li>
                <li><a href="hub.html">Dashboard</a></li>
                <li><a href="#manage-events">Manage Events</a></li>
                <li><a href="#tickets">Tickets</a></li>
                <li><a href="#payment">Payment</a></li>
            </ul>
            <div class="host-rating">
                <h1>Your Host Rating: <span id="host-rating">0-10</span></h1>
            </div>
        </nav>


