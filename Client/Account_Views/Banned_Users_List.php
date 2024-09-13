<?php
include_once("ServiceProvider/Class_Lib/Account_Manager.php");
$accountManager=new Account_Manager();
$banned_user_list=$accountManager->getBannedUsers();
?>
<div id="Banned_Users_List">
        <h1>Your Banned Users</h1>
        <table>
            <thead>
                <tr>
                    <th>User Name</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                <?php foreach($banned_user_list as $banned_user):?> 
                    <td>
                        <?php echo($banned_user["username"])?>
                    </td>
                    <td>
                        <button class="button_default" onclick='unban("<?php echo($banned_user["username"])?>")'>unban?</button>
                    </td>
                <?php endforeach;?>
                </tr>
            </tbody>
        </table>
</div>
<script>

    function unban(banned_user_name){
        var type='POST';
        var data={
            api_function_call:"unban_user",
            banned_user_name:banned_user_name
        };
        var error=$("#error");
        
        $.ajax({
            type: type,
            url: 'http://localhost/project/trustyticket/serviceProvider/API.php',
            data: data , 
            dataType: 'json',
            encode: true
        })
        .done(function(data) {
                window.location.href="http://localhost/project/trustyticket";
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
                console.error('Error:', textStatus, errorThrown);
                console.log(data);
        });
    }
</script>

