UPDATE TABLE events SET event_id=:event_id,name=:name,event_description=:event_description,max_seats=:max_seats,event_start=:event_start,event_end=:event_end,charge=:charge,show_event=:show_event,private_event=:private_event WHERE event_id=:event_id
$statment=$this->conn->prepare($query);
$statment->bindValue(":name",htmlspecialchars($_POST["name"]));
$statment->bindValue(":event_description",htmlspecialchars($_POST["event_description"]));
$statment->bindValue(":max_seats",htmlspecialchars($_POST["max_seats"]));
$statment->bindValue(":event_start",htmlspecialchars($_POST["event_start"]));
$statment->bindValue(":event_end",htmlspecialchars($_POST["event_end"]));
$statment->bindValue(":charge",htmlspecialchars($_POST["charge"]));
$statment->bindValue(":show_event",htmlspecialchars($_POST["show_event"]));
$statment->bindValue(":private_event",htmlspecialchars($_POST["private_event"]));
