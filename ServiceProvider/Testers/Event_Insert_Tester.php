<form id="target" method=POST action="ServiceProvider/API.php" enctype="multipart/form-data">

    <input type="hidden" name="api_function_call" value=insert_event>

    <label for="name">name</label>
    <input type="text" name="name">

    <label for="event_description">event_description</label>
    <textarea name="event_description"></textarea>

    <label for="max_seats">max_seats</label>
    <input type="number" min=1 step="any" max="100" name="max_seats">

    <label for="event_start">event_start</label>
    <input type="datetime-local" name="event_start">

    <label for="event_end">event_end</label>
    <input type="datetime-local" name="event_end">

    <label for="charge">charge</label>
    <input type="number" max="9999" min="0.01"step="0.01"name="charge">

    <div>show_event

        <label for="show_event"> yes </label>
        <input id="show_event" type="radio" name="show_event" value="1">

        <label for="show_event"> no </label>
        <input id="show_event" checked type="radio" name="show_event" value="0"></div>

    <div>private_event

        <label for="private_event"> yes </label>
        <input id="private_event" type="radio" name="private_event" value="1">

        <label for="private_event"> no </label>
        <input id="private_event" checked type="radio" name="private_event" value="0">
    </div>

    <label for="picture">picture</label>
    <input type="file" accept="image/*" name="image">

    <button type="submit">Save</button>
</form>